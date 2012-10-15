/*
* Author:      Marco Kuiper (http://www.marcofolio.net/)
*/
google.load("jquery", "1.4.2");
google.setOnLoadCallback(function()
{
	// Safely inject CSS3 and give the search results a shadow
	var cssObj = { 'box-shadow' : '#888 5px 10px 10px', // Added when CSS3 is standard
		'-webkit-box-shadow' : '#888 5px 10px 10px', // Safari
		'-moz-box-shadow' : '#888 5px 10px 10px'}; // Firefox 3.5+
	$("#suggestions").css(cssObj);
	
	// Fade out the suggestions box when not active
	 $("input").blur(function(){
	 	$('#suggestions').fadeOut();
	 });
});

function lookup(inputString) {
	if(inputString.length == 0) {
		$('#suggestions').fadeOut(); // Hide the suggestions box
	} else {

        $.ajax({
          url: "http://localhost/instagram/meshtiles/searchsuggest/"+inputString+"/tag",
          dataType: 'json',
          success: function(data){
            var item = [];
            var count=0;
            $('#suggestions').fadeIn();
            $.each(data,function(key,value){
                count ++;
                item.push('<a href="<?php echo $this->webroot?>meshtiles/index/'+value[0]+'">');
                item.push('<span class="searchheading">'+value[0]+'</span>');
                item.push('<span>'+value[1]+' Posts</span>');
                item.push('</a>');
                if(count>10)
                    return false;
            });
            $('#suggestions').html('<p id="searchresults">'+item.join('')+'</p>');
          }
        });
	}
}