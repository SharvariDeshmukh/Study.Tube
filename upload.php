<?php require_once("includes/header.php");?>
<?php require_once("includes/classes/VideoDetailsFormProvider.php");?>
<div class="column">
<?php
$formProvider = new VideoDetailsFormProvider($con);
echo $formProvider->createUploadForm();

?>
</div>
<script>
$("form").submit(function(){
    $("#LoadingModal").modal("show");
});



</script>


<!-- Modal -->
<div class="modal fade" id="LoadingModal" tabindex="-1" role="dialog" aria-labelledby="LoadingModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
        Please Wait. This might take a while...
        <img src="assets/Icons/loading_spindle.gif">
      </div>
     
    </div>
  </div>
</div>




<?php require_once("includes/footer.php");?>