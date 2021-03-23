<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BmbxuPwQa2lc/FVzBcNJ7UAyJxM6wuqIj61tLrc4wSX0szH/Ev+nYRRuWlolflfl" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <title>Hello, Scandiweb!</title>
</head>
<body>

<header style="margin-bottom: 50px">
    <div class="container">
        <?php $firstNavItem = ($_SERVER["REQUEST_URI"] == "/product/add") ? "Save"  : "ADD" ?>
        <?php $secondNavItem = ($_SERVER["REQUEST_URI"] == "/product/add") ? "Cancel"  : "MASS DELETE" ?>
        <?php $title = ($_SERVER["REQUEST_URI"] == "/product/add") ? "Add Product"  : "Product List" ?>

        <?php $secondNavRoute = ($_SERVER["REQUEST_URI"] == "/product/add") ? "/product/list" : "#" ?>

        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><?= $title ?></a>
                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link" id="first_nav_item" href="/product/add"><?= $firstNavItem ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="second_nav_item"
                           href="<?= $secondNavRoute ?>"
                            <?php echo $_SERVER["REQUEST_URI"] == "/product/list" ? 'onclick="all_checkboxes()"' : '' ?>>
                            <?= $secondNavItem ?>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>
</header>

<div class="container">
    {{content}}
    <p style="margin-left: 35%">Scandiweb Test assignment</p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta2/dist/js/bootstrap.bundle.min.js" integrity="sha384-b5kHyXgcpbZJO/tY9Ul7kGkf1S0CWuKcCD38l8YkeH8z8QjE0GmW1gYU5S9FOnJ0" crossorigin="anonymous"></script>
</body>
</html>

<script>
    function all_checkboxes()
    {
        let selected_products = [];
        $('.form-check:checkbox:checked').each(function() {
            let element_id = $(this).attr('id')
            selected_products.push(element_id)
            $(`#${element_id}`).parent().remove()
        })
        $.post("/product/delete", {"products_to_delete": selected_products})
    }
</script>