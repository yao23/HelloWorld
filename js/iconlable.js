$('ul li').mouseenter(function(){
    	var image= $(this).find('img'),
        caption = $(this).find('div');
    
    caption.width(image.width());
    caption.height(image.height());
    caption.fadeIn();
	}).mouseleave(function(){
     var image= $(this).find('img'),
        caption = $(this).find('div');
    
    caption.width(image.width());
    caption.height(image.height());
    caption.fadeOut();
		});