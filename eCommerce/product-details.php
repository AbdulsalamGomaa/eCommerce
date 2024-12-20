<?php 

$title = "Product details";
include_once "layouts/header.php";
include_once "layouts/nav.php";
include_once "layouts/breadcrumb.php";
include_once "app/models/Product.php";
include_once "app/models/Spec.php";
include_once "app/models/review.php";

if($_GET) {
    // get specific product according url
    if(isset($_GET['id'])) {
        if(is_numeric($_GET['id'])) {
            // check if id exists in database
            $productObject = new Product;
            $productObject->setStatus(1);
            $productObject->setId($_GET['id']);
            $searchOnProductIdResult = $productObject->searchOnProductId();
            if($searchOnProductIdResult) {
                $product = $searchOnProductIdResult->fetchObject();
            }else {
                header("location:layouts/errors/404.php");die;
            }
        }else {
            header("location:layouts/errors/404.php");die;
        }
    }else {
        header("location:layouts/errors/404.php");die;
    }

}else {
    header("location:layouts/errors/404.php");die;
}

// avg rating
// count reviews 
// + product details => (id,category_name,id,subcategory_name,id,brand_name)
// specs
// get reviews 
// optional : add reviews , related products

$specObject = new Spec;
$specObject->setId($_GET['id']);

$reviewObject = new Review;
$reviewObject->setProduct_id($_GET['id']);

?>
<!-- Product Details Area Start -->
<div class="product-details pt-100 pb-95">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 col-md-12 mr-5">
                <div class="product-details-img">
                    <img class="zoompro" src="assets/img/product/<?= $product->image ?>"/>
                </div>
            </div>
            <div class="col-lg-5 col-md-12">
                <div class="product-details-content">
                    <h4><?= $product->name_en ?></h4>
                    <div class="rating-review">
                        <div class="pro-dec-rating">
                            <?php 
                                for ($i = 1; $i <= 5; $i++) {
                                    if ($i <= $product->reviews_avg ) { ?>
                                        <i class="ion-android-star-outline theme-star"></i>
                                    <?php }else { ?>
                                        <i class="ion-android-star-outline"></i>
                                    <?php } 
                                } ?>
                        </div>
                        <div class="pro-dec-review">
                            <ul>
                                <li><?= $product->reviews_count ?> Reviews </li>
                                <li> Add Your Reviews</li>
                            </ul>
                        </div>
                    </div>
                    <span><?= $product->price ?> EGP</span>
                    <?php 
                        
                        if($product->quantity == 0) {
                            $message = "Out of stock";
                            $color = "danger";
                        }elseif($product->quantity >= 1 && $product->quantity <= 5) {
                            $message = "In stock ($product->quantity) almost finishing";
                            $color = "warning";
                        }else{
                            $message = "In stock";
                            $color = "success";
                        }
                    ?>
                    <div class="in-stock">
                        <p>Available: <span class="text-<?= $color ?>"><?= $message ?></span></p>
                    </div>
                    <p><?= $product->desc_en ?></p>
                    <div class="pro-dec-feature">
                        <ul>
                            <?php
                            $productSpecsResult = $specObject->getProductSpecs();
                            if($productSpecsResult) {
                                $specs = $productSpecsResult->fetchAll(PDO::FETCH_ASSOC);
                                foreach($specs AS $index => $spec) { ?>
                                    <li> <?= $spec['name_en'] ?>: <span><?= $spec['value_en'] ?></span></li>
                                <?php } 
                            } ?>
                        </ul>
                    </div>
                    <div class="quality-add-to-cart">
                        <div class="quality">
                            <label>Qty:</label>
                            <input class="cart-plus-minus-box" type="text" name="qtybutton" value="02">
                        </div>
                        <div class="shop-list-cart-wishlist">
                            <a title="Add To Cart" href="#">
                                <i class="icon-handbag"></i>
                            </a>
                            <a title="Wishlist" href="#">
                                <i class="icon-heart"></i>
                            </a>
                        </div>
                    </div>
                    <div class="pro-dec-categories">
                        <ul>
                            <li class="categories-title">Categories:</li>
                            <li><a href="#"><?= $product->category_name_en ?>,</a></li>
                            <li><a href="shop.php?subcategory=<?= $product->subcategory_id ?>"><?= $product->subcategory_name_en ?>, </a></li>
                            <li><a href="shop.php?brand=<?= $product->brand_id ?>"><?= $product->brand_name_en ?>,</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Product Details Area End -->
<div class="description-review-area pb-70">
    <div class="container">
        <div class="description-review-wrapper">
            <div class="description-review-topbar nav text-center">
                <a class="active" data-toggle="tab" href="#des-details1">Description</a>
                <a data-toggle="tab" href="#des-details3">Review</a>
            </div>
            <div class="tab-content description-review-bottom">
                <div id="des-details1" class="tab-pane active">
                    <div class="product-description-wrapper">
                        <p><?= $product->desc_en ?></p> 
                    </div>
                </div>
                <div id="des-details3" class="tab-pane">
                    <div class="rattings-wrapper">
                        <?php
                        $userReviewsResult = $reviewObject->getUserReviews();
                        if($userReviewsResult) {
                            $reviews = $userReviewsResult->fetchAll(PDO::FETCH_ASSOC);
                            foreach($reviews AS $index => $review) { ?>
                                <div class="sin-rattings">
                                    <div class="star-author-all">
                                        <div class="ratting-star f-left">
                                            <?php 
                                                for($i = 1; $i <= $review['value']; $i++) { ?>
                                                    <i class="ion-star theme-color"></i>
                                                <?php }
                                                for($i = 1; $i <= 5-$review['value']; $i++) { ?>
                                                    <i class="ion-android-star-outline"></i>
                                                <?php }
                                            ?>
                                            <span>(<?= $review['value'] ?>)</span>
                                        </div>
                                        <div class="ratting-author f-right">
                                            <h3><?= $review['full_name'] ?></h3>
                                            <span><?= $review['created_at'] ?></span>
                                        </div>
                                    </div>
                                    <p><?= $review['comment'] ?></p>
                                </div>
                            <?php } 
                        }else {
                            echo "<div class='alert alert-warning text-center'><strong>No reviews for this product yet</strong></div>";
                        } 
                        ?>
                    </div>
                    <?php
                    if(isset($_SESSION['user'])) { ?>
                        <div class="ratting-form-wrapper">
                            <h3>Add your Comments :</h3>
                            <div class="ratting-form">
                                <form action="#">
                                    <div class="star-box">
                                        <h2>Rating:</h2>
                                        <div class="ratting-star">
                                            <i class="ion-star theme-color"></i>
                                            <i class="ion-star theme-color"></i>
                                            <i class="ion-star theme-color"></i>
                                            <i class="ion-star theme-color"></i>
                                            <i class="ion-star"></i>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="rating-form-style mb-20">
                                                <input placeholder="Name" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="rating-form-style mb-20">
                                                <input placeholder="Email" type="text">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="rating-form-style form-submit">
                                                <textarea name="message" placeholder="Message"></textarea>
                                                <input type="submit" value="add review">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php }?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="product-area pb-100">
    <div class="container">
        <div class="product-top-bar section-border mb-35">
            <div class="section-title-wrap">
                <h3 class="section-title section-bg-white">Related Products</h3>
            </div>
        </div>
        <div class="featured-product-active hot-flower owl-carousel product-nav">
            <div class="product-wrapper">
                <div class="product-img">
                    <a href="product-details.html">
                        <img alt="" src="assets/img/product/product-1.jpg">
                    </a>
                    <span>-30%</span>
                    <div class="product-action">
                        <a class="action-wishlist" href="#" title="Wishlist">
                            <i class="ion-android-favorite-outline"></i>
                        </a>
                        <a class="action-cart" href="#" title="Add To Cart">
                            <i class="ion-ios-shuffle-strong"></i>
                        </a>
                        <a class="action-compare" href="#" data-target="#exampleModal" data-toggle="modal"
                            title="Quick View">
                            <i class="ion-ios-search-strong"></i>
                        </a>
                    </div>
                </div>
                <div class="product-content text-left">
                    <div class="product-hover-style">
                        <div class="product-title">
                            <h4>
                                <a href="product-details.html">Le Bongai Tea</a>
                            </h4>
                        </div>
                        <div class="cart-hover">
                            <h4><a href="product-details.html">+ Add to cart</a></h4>
                        </div>
                    </div>
                    <div class="product-price-wrapper">
                        <span>$100.00 -</span>
                        <span class="product-price-old">$120.00 </span>
                    </div>
                </div>
            </div>
            <div class="product-wrapper">
                <div class="product-img">
                    <a href="product-details.html">
                        <img alt="" src="assets/img/product/product-2.jpg">
                    </a>
                    <div class="product-action">
                        <a class="action-wishlist" href="#" title="Wishlist">
                            <i class="ion-android-favorite-outline"></i>
                        </a>
                        <a class="action-cart" href="#" title="Add To Cart">
                            <i class="ion-ios-shuffle-strong"></i>
                        </a>
                        <a class="action-compare" href="#" data-target="#exampleModal" data-toggle="modal"
                            title="Quick View">
                            <i class="ion-ios-search-strong"></i>
                        </a>
                    </div>
                </div>
                <div class="product-content text-left">
                    <div class="product-hover-style">
                        <div class="product-title">
                            <h4>
                                <a href="product-details.html">Society Ice Tea</a>
                            </h4>
                        </div>
                        <div class="cart-hover">
                            <h4><a href="product-details.html">+ Add to cart</a></h4>
                        </div>
                    </div>
                    <div class="product-price-wrapper">
                        <span>$100.00 -</span>
                        <span class="product-price-old">$120.00 </span>
                    </div>
                </div>
            </div>
            <div class="product-wrapper">
                <div class="product-img">
                    <a href="product-details.html">
                        <img alt="" src="assets/img/product/product-3.jpg">
                    </a>
                    <span>-30%</span>
                    <div class="product-action">
                        <a class="action-wishlist" href="#" title="Wishlist">
                            <i class="ion-android-favorite-outline"></i>
                        </a>
                        <a class="action-cart" href="#" title="Add To Cart">
                            <i class="ion-ios-shuffle-strong"></i>
                        </a>
                        <a class="action-compare" href="#" data-target="#exampleModal" data-toggle="modal"
                            title="Quick View">
                            <i class="ion-ios-search-strong"></i>
                        </a>
                    </div>
                </div>
                <div class="product-content text-left">
                    <div class="product-hover-style">
                        <div class="product-title">
                            <h4>
                                <a href="product-details.html">Green Tea Tulsi</a>
                            </h4>
                        </div>
                        <div class="cart-hover">
                            <h4><a href="product-details.html">+ Add to cart</a></h4>
                        </div>
                    </div>
                    <div class="product-price-wrapper">
                        <span>$100.00 -</span>
                        <span class="product-price-old">$120.00 </span>
                    </div>
                </div>
            </div>
            <div class="product-wrapper">
                <div class="product-img">
                    <a href="product-details.html">
                        <img alt="" src="assets/img/product/product-4.jpg">
                    </a>
                    <div class="product-action">
                        <a class="action-wishlist" href="#" title="Wishlist">
                            <i class="ion-android-favorite-outline"></i>
                        </a>
                        <a class="action-cart" href="#" title="Add To Cart">
                            <i class="ion-ios-shuffle-strong"></i>
                        </a>
                        <a class="action-compare" href="#" data-target="#exampleModal" data-toggle="modal"
                            title="Quick View">
                            <i class="ion-ios-search-strong"></i>
                        </a>
                    </div>
                </div>
                <div class="product-content text-left">
                    <div class="product-hover-style">
                        <div class="product-title">
                            <h4>
                                <a href="product-details.html">Best Friends Tea</a>
                            </h4>
                        </div>
                        <div class="cart-hover">
                            <h4><a href="product-details.html">+ Add to cart</a></h4>
                        </div>
                    </div>
                    <div class="product-price-wrapper">
                        <span>$100.00 -</span>
                        <span class="product-price-old">$120.00 </span>
                    </div>
                </div>
            </div>
            <div class="product-wrapper">
                <div class="product-img">
                    <a href="product-details.html">
                        <img alt="" src="assets/img/product/product-5.jpg">
                    </a>
                    <span>-30%</span>
                    <div class="product-action">
                        <a class="action-wishlist" href="#" title="Wishlist">
                            <i class="ion-android-favorite-outline"></i>
                        </a>
                        <a class="action-cart" href="#" title="Add To Cart">
                            <i class="ion-ios-shuffle-strong"></i>
                        </a>
                        <a class="action-compare" href="#" data-target="#exampleModal" data-toggle="modal"
                            title="Quick View">
                            <i class="ion-ios-search-strong"></i>
                        </a>
                    </div>
                </div>
                <div class="product-content text-left">
                    <div class="product-hover-style">
                        <div class="product-title">
                            <h4>
                                <a href="product-details.html">Instant Tea Premix</a>
                            </h4>
                        </div>
                        <div class="cart-hover">
                            <h4><a href="product-details.html">+ Add to cart</a></h4>
                        </div>
                    </div>
                    <div class="product-price-wrapper">
                        <span>$100.00 -</span>
                        <span class="product-price-old">$120.00 </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
include_once "layouts/footer.php";
include_once "layouts/footer-scripts.php";
?>