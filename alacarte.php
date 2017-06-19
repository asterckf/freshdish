<?php
require_once 'core/init.php';
include 'includes/head.php' ;
include 'includes/navigation.php' ;
include 'includes/headerfull.php';
include 'includes/leftbar.php';

$sql = "SELECT * FROM products WHERE categories = 2 AND deleted = 0";
$featured = $db->query($sql);

?>


<!-- Main content -->
<div class="col-md-8">
  <div class="row">
    <h3 class="text-left">MENU</h3>

    <div class="row">
    <?php while($product = mysqli_fetch_assoc($featured)):?>

        <div class="col-md-4">
          <h4><?= $product['name']; ?></h4>
          <img src="<?= $product['image']; ?>" alt=<?= $product['name']; ?> class="img-thumb">
          <p class="price">RM <?= $product['price']; ?></p>
          <button type="button" class="btn btn-sm btn-success" onclick="detailsmodal(<?= $product['id'];?>)">
            Details</button>
          </div>

        <?php endwhile; ?>
        </div>

    </div>

<?php
      include 'includes/rightbar.php';
      include 'includes/footer.php';
?>
