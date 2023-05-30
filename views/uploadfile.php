<div class="wrap">
<form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
    <input type="hidden" name="action" value="process_csv_upload">
    <input type="file" name="csv_file">
    <input type="submit" value="Upload" style="background-color:green;">
</form>
</div>
     
