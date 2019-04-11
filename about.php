<?php
include_once '../src/session.php';
?>

<!-- This is the code to display 'about' the company  --------->
<!doctype html>
<html lang="en">
<head>
    <style>
        @import "../css/body.css"</style>
    <title>Gluten Free Food.About the Company</title>

    <!-- display the header pages ------->
    <?php require_once '../templates/_header.php';?>
    <?php require_once '../templates/header_top_3.php'; ?>
    <?php require_once '../templates/navigation.php';?>
</head>

<body>

<div id="body_container">

    <!----------------SECTION ITEM START-, DISPLAYS THE LEFT ASIDE MENU---------->

    <section class="link_menu_left">
        <h1>Customer Menu</h1>

        <ul class = "nav_menu_left">
            <li><a href='../public/index.php'>Home</a></li>
            <li><a href='../src/main.php'>Login</a></li>
            <li><a href='../src/logout.php'>Logout</a></li>
            <li><a href='../src/admin_login.php'>Administration</a></li>
            <li><a href='cart_display.php'>View Cart</a></li>
            <li><a href='../src/change_pass.php'>Change Password</a></li>
            <li><a href='../src/wish_table.php'>Wish List</a></li>
            <li><a href='../src/update_user_profile.php'>Update Profile</a></li>
        </ul>

    </section>


    <!----------------SECTION ITEM FINISH----------->

    <!----------------ARTICLE ITEM START -- DISPLAY COMPANY INFO----------->

    <article class="blog_middle">
        <h1>Gluten Free Products</h1>
        <h2>This is about the company. Established in 2009.</h2>
    </article>

</body>

<!--------- CALLS THE FOOTER PAGE CODE ------->

<?php require_once '../templates/footer_one.php'; ?>
</html>