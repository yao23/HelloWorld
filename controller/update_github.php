<?php
require "init.php";
define('GIT_SERVICE_ID',3);
define('GIT_SERVICE_NAME','GitHub');
define('GIT_CLIENT_ID','a701c6d3f8b183a704b8');
define('GIT_CLIENT_SECRET',   '5722ea24b95ccfca806415fb31d123bc44cea431');
define('GIT_OWNER_FACTOR', 0.01 );
define('GIT_NON_OWNER_FACTOR', 0.003 );
define('GIT_REDIRECT_URI', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);
define('GIT_SCOPE',        'repo' );

function gitAuth(){
    //var_dump(GIT_REDIRECT_URI);
    //session_name('github');
    //session_start();
    // OAuth 2 Control Flow
    if (isset($_GET['error'])) {
        // stackoverflow returned an error
        print $_GET['error'] . ': ' . $_GET['error_description'];
        exit;
    } elseif (isset($_GET['code'])) {
        // User authorized your application
        if ($_SESSION['state'] == $_GET['state']) {
            // Get token so you can make API calls
            getGitAccessToken();
        } else {
            // CSRF attack? Or did you mix up your states?
            exit;
        }
    } else { 
        if ((empty($_SESSION['git_expires_at'])) || (time() > $_SESSION['git_expires_at'])) {
            // Token has expired, clear the state
            $_SESSION = array();
        }
        if (empty($_SESSION['git_access_token'])) {
            // Start authorization process
            $_SESSION['uid']=$_GET['uid'];
            getGitAuthCode();
        }
    }
}
//step 1:get auth code
function getGitAuthCode() {
    $params = array('client_id' => GIT_CLIENT_ID,
                    'scope' => GIT_SCOPE,
                    'state' => uniqid('', true), // unique long string
                    'redirect_uri' => GIT_REDIRECT_URI,
              );
 
    // Authentication request
    $url = 'https://github.com/login/oauth/authorize?' . http_build_query($params);
     
    // Needed to identify request when it returns to us
    $_SESSION['state'] = $params['state'];
 
    // Redirect user to authenticate
    header("Location: $url");
    exit;
}

//step2: get token
function getGitAccessToken() {
    $params = array(
                    'client_id' => GIT_CLIENT_ID,
                    'client_secret' => GIT_CLIENT_SECRET,
                    'code' => $_GET['code'],
                    'redirect_uri' => GIT_REDIRECT_URI,
              );
     
    // Access Token request
    $url = 'https://github.com/login/oauth/access_token?' . http_build_query($params);
     
    // Tell streams to make a POST request
    $context = stream_context_create(
                    array('http' => 
                        array('method' => 'POST',
                              'header' => 'Content-Length: 0' // for PHP < 5.3 bug fix
                        )
                    )
                );
 
    // Retrieve access token information
    $response = file_get_contents($url, false, $context);
    // response format is access_token=eVKgZxW3dFPuVjNSRzGGKg))&expires=86400 
    $raw_token = explode('&',$response);
    $token = explode('=',$raw_token[0]);
    $expires = explode('=',$raw_token[1]);
    $_SESSION['git_access_token'] = $token[1]; // guard this! 
    $_SESSION['git_expires_in']   = $expires[1]; // relative time (in seconds)
    $_SESSION['git_expires_at']   = time() + $_SESSION['git_expires_in']; // absolute time
    //var_dump($token);

     
    return true;
}

function fetch($id,$order='desc',$sort='activity'){
        $context = stream_context_create(
                    array('http' =>array(
                               'method' => 'GET',
                               'header' => 'Accept-Encoding: gzip, deflate 
                                            Content-Type: text/html; charset=UTF-8',
                                         )
                         )
                   );
        //add compress.zlib://  to read the gziped response from stackoverflow
        $url_questions = 'compress.zlib://https://api.stackexchange.com/2.1/users/'.$id.'/answers?order='.$order.'&sort='.$sort.'&site=stackoverflow';
       
        $response = file_get_contents($url_questions, false, $context);
        return json_decode($response);
}

/**
 * use token to get a specific user info
 * @param type $accece_token
 * @param type $order
 * @param type $sort
 * @return type 
 */
function getUserInfo($access_token,$context){
        //add compress.zlib://  to read the gziped response from stackoverflow
        $url_user = 'https://api.github.com/user';
        $response = file_get_contents($url_user, false, $context);
        return json_decode($response);
}

function isOwner($user,$repo){
    if($user->id == $repo->owner->id)
        return true;
    return false;
}

function updateAll($access_token,$uid){
    $context = stream_context_create(
                    array('http' =>array(
                               'method' => 'GET',
                               'header' => 'Authorization: token '.$access_token
                                )
                         )
                   );
    //statistics skills
    $repo_url= 'https://api.github.com/user/repos';
    $response = file_get_contents($repo_url, false, $context);
    $repos=json_decode($response);
    $git_user=getUserInfo($access_token,$context);
    $skill_score = array();
    foreach($repos as $repo){
        $language = $repo->language;
        if(!isset($skill_score[$language]))
            $skill_score[$language] = 0;
        if(isOwner($git_user, $repo)){
            $skill_score[$language] += GIT_OWNER_FACTOR*$repo->size * ($repo->forks_count + 0.1*$repo->watchers+1);
        }else{
            $skill_score[$language] += GIT_NON_OWNER_FACTOR*$repo->size * ($repo->forks_count + 0.1*$repo->watchers+1);
        }
    }
    //var_dump($skill_score);
    //update db
    foreach($skill_score as $skill => $score){
        
        $skill_id = Skill::getSkillByName($skill);
        $user_has_skill_id = Skill::getUserSkillById($skill_id, $uid);
        //var_dump($skill_id);
        //var_dump($user_has_skill_id);
        $proof_name = GIT_SERVICE_NAME.','.$skill.',language';
        $proof_id = ProofItem::getProofId(GIT_SERVICE_ID, $proof_name);
        $skillservice = new Skill($user_has_skill_id, $uid, $skill_id, 0, 1);
        $skillservice->updateStrength($proof_id, $user_has_skill_id, $score);
    }
}

//login check
if(!isset($_SESSION['user']))
    header("location:/helloworld/login.php");
else
    $UID = $_SESSION['user']->getUid(); 

    //authetication
    gitAuth();
    $GIT_TOKEN = $_SESSION['git_access_token'];
    $UID = $_SESSION['uid'];
    updateAll($GIT_TOKEN,$UID);
    //var_dump($UID);
    header("location:/helloworld/profile.php?uid=".$UID);
?>
