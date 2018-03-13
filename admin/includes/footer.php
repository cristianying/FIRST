<footer class="text-center" id="footer">&copy; Copyright 2013-2015</footer>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>﻿
<script >
function updateSizes(){
    var sizeString = '';
    for (var i=1;i<=12;i++){
      if(jQuery('#size'+i).val()!= ''){
        sizeString += jQuery('#size'+i).val()+':'+ jQuery('#quantity'+i).val()+',';
      }
    }
    jQuery('#sizes').val(sizeString);
  }
function get_child_option(selected){
  if(typeof selected ==='undefined'){
    var selected ='';
  }
  var parentID= $('#parent').val();
  $.ajax({
    url: '/tutorial/admin/parsers/child_categories.php',
    type: 'POST',
    //data passed to child_categories as parentID: 'value'
    data: {parentID:parentID, selected: selected},
    success: function(data){
      console.log(data);
      $('#child').html(data);
    },
    error: function(){
      alert("something went wrong with the child option."
    )},
  });

}
$('select[name="parent"]').change(function(){
  get_child_option();
});

</script>
</body>
</html>
