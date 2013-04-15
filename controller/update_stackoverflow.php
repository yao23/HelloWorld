<?php
require "init.php";

define('STACK_SERVICE_ID',2);
define('STACK_SERVICE_NAME','StackOverflow');
define('STACK_CLIENT_KEY','RhRGTt7udNhTDRUnwxe4Bg((');
define('STACK_API_KEY',      '1425'   );
define('STACK_API_SECRET',   'WWk1JC9l3CnqpLbKejxOJg((');
define('STACK_REDIRECT_URI', 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']);
define('STACK_SCOPE',        'private_info' );
define('ANSWER_SOCRE', 10);
define('ACCEPT_SOCRE', 50);
define('QUESTION_SOCRE', 5);
/**
 * Example:
 * https://api.stackexchange.com/2.1/users/1019448/answers?order=desc&sort=activity&site=stackoverflow 
 */


    

function stackAuth(){
    //session_name('stack_overflow');
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
            getStackAccessToken();
        } else {
            // CSRF attack? Or did you mix up your states?
            exit;
        }
    } else { 
        if ((empty($_SESSION['stack_expires_at'])) || (time() > $_SESSION['stack_expires_at'])) {
            // Token has expired, clear the state
            $_SESSION = array();
        }
        if (empty($_SESSION['stack_access_token'])) {
            // Start authorization process
            $_SESSION['uid']=$_GET['uid'];
            getStackAuthCode();
        }
    }
}
//step 1:get auth code
function getStackAuthCode() {
    $params = array('client_id' => STACK_API_KEY,
                    'scope' => STACK_SCOPE,
                    'state' => uniqid('', true), // unique long string
                    'redirect_uri' => STACK_REDIRECT_URI,
              );
 
    // Authentication request
    $url = 'https://stackexchange.com/oauth?' . http_build_query($params);
     
    // Needed to identify request when it returns to us
    $_SESSION['state'] = $params['state'];
 
    // Redirect user to authenticate
    header("Location: $url");
    exit;
}

//step2: get token
function getStackAccessToken() {
    $params = array(
                    'client_id' => STACK_API_KEY,
                    'client_secret' => STACK_API_SECRET,
                    'code' => $_GET['code'],
                    'redirect_uri' => STACK_REDIRECT_URI,
              );
     
    // Access Token request
    $url = 'https://stackexchange.com/oauth/access_token?' . http_build_query($params);
     
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
    $_SESSION['stack_access_token'] = $token[1]; // guard this! 
    $_SESSION['stack_expires_in']   = $expires[1]; // relative time (in seconds)
    $_SESSION['stack_expires_at']   = time() + $_SESSION['stack_expires_in']; // absolute time
    //var_dump($token);

     
    return true;
}

/**
 * use token to get a specific user info
 * @param type $accece_token
 * @param type $order
 * @param type $sort
 * @return type 
 */
function getUserInfo($accece_token,$order='desc',$sort='reputation'){
        $context = stream_context_create(
                    array('http' =>array(
                               'method' => 'GET',
                               'header' => 'Accept-Encoding: gzip, deflate'
                                )
                         )
                   );
        //add compress.zlib://  to read the gziped response from stackoverflow
        $url_user = 'compress.zlib://https://api.stackexchange.com/2.1/me?key='.STACK_CLIENT_KEY.'&order='.$order.'&sort='.$sort.'&site=stackoverflow&access_token='.$accece_token.'&filter=default';
        //var_dump($url_user);
        $response = file_get_contents($url_user, false, $context);
        return json_decode($response);
}

function getAnswersByUser($stack_id,$context){
        //add compress.zlib://  to read the gziped response from stackoverflow
        $url_questions = 'compress.zlib://https://api.stackexchange.com/2.1/users/'.$stack_id.'/answers?key='.STACK_CLIENT_KEY.'&site=stackoverflow';
        //var_dump($url_questions);
        $response = file_get_contents($url_questions, false, $context);
        return json_decode($response);
}

function getQuestionByUser($stack_id,$context){
        //add compress.zlib://  to read the gziped response from stackoverflow
        $url_questions = 'compress.zlib://https://api.stackexchange.com/2.1/users/'.$stack_id.'/questions?key='.STACK_CLIENT_KEY.'&site=stackoverflow';
        //var_dump($url_questions);
        $response = file_get_contents($url_questions, false, $context);
        return json_decode($response);
}

function getQuestionTags($qid,$context){
        //add compress.zlib://  to read the gziped response from stackoverflow
        $url_questions = 'compress.zlib://https://api.stackexchange.com/2.1/questions/'.$qid.'/?key='.STACK_CLIENT_KEY.'&site=stackoverflow&filter=default';
        //var_dump($url_questions);
        $question = json_decode(file_get_contents($url_questions, false, $context));
        //var_dump($question);
        if($question!=NULL){
            return $question->items[0]->tags;
        }
}

function updateAllSkills($token,$uid,$stack_id){
    $context = stream_context_create(
                    array('http' =>array(
                               'method' => 'GET',
                               'header' => 'Accept-Encoding: gzip, deflate'
                                )
                         )
                   );
    $answers = getAnswersByUser($stack_id,$context);
    $questions = getQuestionByUser($stack_id,$context);
    //var_dump($questions);
    //statistics all answers
    $tag_count = array();
    foreach ($answers->items as $answer){
        $question_tags = getQuestionTags($answer->question_id,$context);
        foreach($question_tags as $tag){
            if(!isset($tag_count[$tag])){
                $tag_count[$tag]=0;
            }
            if($answer->is_accepted)
                $tag_count[$tag]+=ACCEPT_SOCRE;
            $tag_count[$tag]+=ANSWER_SOCRE;
        }
    }
    
  
    //update answer strength
    foreach($tag_count as $skill => $score){
        
        $skill_id = Skill::getSkillByName($skill);
        $user_has_skill_id = Skill::getUserSkillById($skill_id, $uid);
        //var_dump($skill_id);
        //var_dump($user_has_skill_id);
        $proof_name = STACK_SERVICE_NAME.','.$skill.',answer';
        $proof_id = ProofItem::getProofId(STACK_SERVICE_ID, $proof_name);
        $skillservice = new Skill($user_has_skill_id, $uid, $skill_id, 0, 1);
        $skillservice->updateStrength($proof_id, $user_has_skill_id, $score);
    }
    
    //update question strength
    $tag_count = array();
    foreach($questions->items as $question){
        foreach($question->tags as $tag){
            if(!isset($tag_count[$tag])){
                $tag_count[$tag]=0;
            }
            $tag_count[$tag]+=QUESTION_SOCRE*$question->score;
        }
    }
    //var_dump($tag_count);
    //update question strength
    foreach($tag_count as $skill => $score){
        
        $skill_id = Skill::getSkillByName($skill);
        $user_has_skill_id = Skill::getUserSkillById($skill_id, $uid);
        //var_dump($skill_id);
        //var_dump($user_has_skill_id);
        $proof_name = STACK_SERVICE_NAME.','.$skill.',question';
        $proof_id = ProofItem::getProofId(STACK_SERVICE_ID, $proof_name);
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
stackAuth();
$STACK_TOKEN = $_SESSION['stack_access_token'];
$UID = $_SESSION['uid'];
$stack_user = getUserInfo($STACK_TOKEN);
updateAllSkills($STACK_TOKEN,$UID,$stack_user->items[0]->user_id);
//var_dump($STACK_TOKEN);
header("location:/helloworld/profile.php?uid=".$UID);
?>
