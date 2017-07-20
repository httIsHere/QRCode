<?php 
// public function fileupsend(){
	$img = $_POST['data'];
    $type_pic = $img->file_upload('1',array('jpg', 'gif', 'png', 'jpeg'),'filetest','myfile');
    echo $type_pic['img_path'];

// }
// fileupsend();
?>