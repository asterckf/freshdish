<?php
require_once $_SERVER['DOCUMENT_ROOT'].'/freshdish/core/init.php';
include 'includes/head.php';
include 'includes/navigation.php';
//Delete product
if(isset($_GET['delete'])){
  $id = sanitize($_GET['delete']);
  $db->query("UPDATE products SET deleted = 1 WHERE id ='$id'");
  header('Location: products.php');
}

$dbpath = '';
if (isset($_GET['add']) || isset($_GET['edit'])){
$name = ((isset($_POST['name']) && $_POST['name'] != '')?sanitize($_POST['name']):'');
$price = ((isset($_POST['price']) &&  $_POST['price'] != '')?sanitize($_POST['price']):'');
$categories = ((isset($_POST['categories']) &&  $_POST['categories'] != '')?sanitize($_POST['categories']):'');
$description = ((isset($_POST['description']) &&  $_POST['description'] != '')?sanitize($_POST['description']):'');
$saved_image = '';

  if(isset($_GET['edit'])){
    $edit_id = (int)$_GET['edit'];
    $produtResult = $db->query("SELECT * FROM products WHERE id ='$edit_id' ");
    $product = mysqli_fetch_assoc($produtResult);
    if (isset($_GET['delete_image'])){
      $image_url = $_SERVER['DOCUMENT_ROOT'].$product['image'];echo $image_url;
      unlink($image_url);
      $db->query("UPDATE products SET image = '' WHERE id = '$edit_id'");
      header('Location: products.php?edit='.$edit_id);
    }
    $name =((isset($_POST['name'])) && !empty($_POST['name'])) ?sanitize($_POST['name']): $product['name'];
    $price =((isset($_POST['price'])) && !empty($_POST['price'])) ?sanitize($_POST['price']): $product['price'];
    $categories =((isset($_POST['categories'])) && !empty($_POST['categories'])) ?sanitize($_POST['categories']): $product['categories'];
    $description =((isset($_POST['description'])) && !empty($_POST['description'])) ?sanitize($_POST['description']): $product['description'];
    $saved_image = (($product['image']!= '')?$product['image']: '');
    $dbpath = $saved_image;
  }
  if ($_POST){
    $dbpath = '';
    $errors= array();
    $required = array('name','price','categories');
    foreach($required as $field){
      if($_POST[$field] == ''){
        $errors[] = 'All fields with * are required.';
        break;
      }
    }
    if (!empty($_FILES)){
      var_dump($_FILES);
      $image = $_FILES['image'];
      $nameImage = $image['name'];
      $nameArray = explode('.',$nameImage);
      $fileName = $nameArray[0];
      $fileExt = $nameArray[1];
      $mime = explode('/',$image['type']);
      $mimeType = $mime[0];
      $mimeExt = $mime[1];
      $tmpLoc = $image['tmp_name'];
      $fileSize = $image['size'];
      $allowed = array('png','jpg','jpeg','gif');
      $uploadName = md5(microtime()).'.'.$fileExt;
      $uploadPath = BASEURL. 'images/menu/'.$uploadName;
      $dbpath = '/freshdish/images/menu/'.$uploadName;
      if ($mimeType != 'image'){
        $errors[] = 'The file must be an image.';
      }
      if (!in_array($fileExt, $allowed)){
        $errors[] = 'The file extension must be a png, jpg, jpeg, or gif';
      }
      if ($fileSize > 1500000){
        $errors[] = 'The file size must be under 15MB.';
      }
      if ($fileExt != $mimeExt && ($mimeExt == 'jpeg' && $fileExt != 'jpg')){
        $errors[] = 'File extension does not match the file';
      }
    }
    if(!empty($errors)){
      echo display_errors($errors);
    }else{
      move_uploaded_file($tmpLoc,$uploadPath);
      $insertSql = "INSERT INTO products(name,price,categories,description,image)
      VALUES ('$name','$price','$categories','$description','$dbpath')";
      if(isset($_GET['edit'])){
        $insertSql = "UPDATE products SET name = '$name', price ='$price' ,
        categories ='$categories' , description ='$description', image = '$dbpath'
        WHERE id = '$edit_id'";
      }

      $db->query($insertSql);
      header('Location: products.php');
    }
  }
?>
<h2 class="text-center"><?= ((isset($_GET['edit'])))?'Edit' :'Add A New'?> Product</h2><hr>
<div class="col-md-3"></div>
<div class="col-md-8">
<form action="products.php?<?=((isset($_GET['edit']))?'edit='.$edit_id:'add=1');?>" method="POST" enctype="multipart/form-data">
  <div class="form-group col-md-8">
    <label for="name">Name*:</label>
    <input type="text" name="name" class="form-control" id="name" value="<?=$name;?>">
  </div>

  <div class="form-group col-md-8">
    <label for="price">Price*:</label>
    <input type="text" name="price" class="form-control" id="price" value="<?=$price; ?>">
  </div>

  <div class="form-group col-md-8">
    <label for="categories">Category*:</label>
    <input type="text" name="categories" class="form-control" id="categories" value="<?=$categories;?>">
  </div>

  <div class="form-group col-md-8">
    <?php if ($saved_image != ''):?>
      <div class="saved-image"><img src="<?=$saved_image;?>" alt="saved image"/><br>
        <a href="products.php?delete_image=1&edit=<?=$edit_id;?>" class="text-danger">Delete Image</a>
      </div>
    <?php else: ?>
    <label for="image">Image:</label>
    <input type="file" name="image" class="form-control" id="image">
    <?php endif; ?>
  </div>

  <div class="form-group col-md-8">
    <label for="description">Description:</label>
    <textarea name="description" class="form-control" id="description" rows="6"><?=$description; ?></textarea>
  </div>

  <div class="col-md-8">
    <a href="products.php" class="btn btn-default">Cancel</a>
    <input type="submit" value="<?=(isset($_GET['edit']))?'Edit' :'Add' ?> Product" class="btn btn-success">
  </div><div class="clearfix"></div>
</form>
</div>
<div class="col-md-3"></div>

<?php } else{
//get products database
$sql = "SELECT * FROM products WHERE deleted = 0 ";
$results = $db->query($sql);
?>

<h2 class="text-center">Products</h2>
<a href="products.php?add=1" class="btn btn-success pull-right" id="add-product-btn">Add Product</a>
<div class ="clearfix"></div>
<hr>
<table class ="table table-bordered table-condensed table-striped">
  <thead>
    <th></th><th>Product</th><th>Price</th><th>Category</th><th>Image</th><th>Description</th><th>Sold</th>
  </thead>
  <tbody>
    <?php while($product = mysqli_fetch_assoc($results)): ?>
      <tr>
      <td>
        <a href="products.php?edit=<?= $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-pencil"></span></a>
        <a href="products.php?delete=<?= $product['id'];?>" class="btn btn-xs btn-default"><span class="glyphicon glyphicon-remove"></span></a>
      </td>
      <td><?= $product['name'];?></td>
      <td><?= money($product['price']);?></td>
      <td><?= $product['categories'];?></td>
      <td><?= $product['image'];?></td>
      <td><?= $product['description'];?></td>
      <td></td>
    </tr>
  <?php endwhile; ?>
  </tbody>
</table>

<?php } include 'includes/footer.php';?>
