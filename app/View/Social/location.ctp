<button id="search">Search</button>
<?php echo $this->Html->script('jquery-1.7.1.min'); ?>
<script type="text/javascript">
    $('#search').click(function(){
        getLocation();  
    });        
    function getLocation()
    {
        if (navigator.geolocation)
        {
            navigator.geolocation.getCurrentPosition(showPosition);
        }
    
    }
    function showPosition(position)
    {
      window.location.href="<?php  echo $this->webroot?>social/facebook_location/"+position.coords.latitude+'/'+ position.coords.longitude;
      console.log(position.coords.longitude);	
    }
</script>