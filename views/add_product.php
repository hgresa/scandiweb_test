<form action="" method="post" id="product_form">
    <div class="form-group row">
        <!-- ------------------------------------------------------------------------------------------------------>
        <div style="display: flex">
            <div class="mb-3 col-md-1">
                <label for="sku" class="form-label">SKU</label>
            </div>
            <div class="mb-3 col-md-3">
                <input name="sku" type="text" class="form-control" id="sku" onkeyup="validateForm('sku')">
                <?php if (isset($errors["sku"])) : ?>
                    <span id="sku-err" style="color: red"><?= $errors["sku"] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- ------------------------------------------------------------------------------------------------------>
        <div style="display: flex">
            <div class="mb-3 col-md-1">
                <label for="name" class="form-label">Name</label>
            </div>
            <div class="mb-3 col-md-3">
                <input name="name" type="text" class="form-control" id="name" onkeyup="validateForm('name')">
                <?php if (isset($errors["name"])) : ?>
                    <span id="name-err" style="color: red"><?= $errors["name"] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- ------------------------------------------------------------------------------------------------------>
        <div style="display: flex">
            <div class="mb-3 col-md-1">
                <label for="price" class="form-label">Price</label>
            </div>
            <div class="mb-3 col-md-3">
                <input name="price" type="text" class="form-control" id="price" onkeyup="validateForm('price');isNaturalNum('price')">
                <?php if (isset($errors["price"])) : ?>
                    <span id="price-err" style="color: red"><?= $errors["price"] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- ------------------------------------------------------------------------------------------------------>
        <div style="display: flex">
            <div class="mb-3 col-md-1">
                <label for="type" class="form-label">Type Switcher</label>
            </div>
            <div class="mb-3 col-md-3">
                <select name="type" class="form-select" id="type" onchange="switch_type();validateForm('type')">
                    <option value=""></option>
                    <option value="dvd">DVD-disc</option>
                    <option value="furniture">Furniture</option>
                    <option value="book">Book</option>
                </select>
                <?php if (isset($errors["type"])) : ?>
                    <span id="type-err" style="color: red"><?= $errors["type"] ?></span>
                <?php endif; ?>
            </div>
        </div>
        <!-- ------------------------------------------------------------------------------------------------------>
        <div id="type_form"></div>
        <!-- ------------------------------------------------------------------------------------------------------>
    </div>
</form>

<script>
    function switch_type()
    {
        let type = $("#type").val()
        let type_form = $("#type_form")
            type_form.empty()

        if (type === "dvd")
        {
                type_form.append
                (
                    `<div style="display: flex">
                        <div class="mb-3 col-md-1"><label for="size_mb" class="form-label">Size (MB)</label></div>
                        <div class="mb-3 col-md-3">
                            <input name="size_mb" type="text" class="form-control" id="size_mb" aria-describedby="discHelp" onkeyup="validateForm('size_mb');isNaturalNum('size_mb')">
                            <?php if (isset($errors["size_mb"])) : ?>
                                <span style="color: red"><?= $errors["size_mb"] ?></span>
                            <?php endif; ?>
                        <div id="discHelp" class="form-text">Please provide size in MegaBytes</div>
                     </div></div>`
                )
        }
        else if (type === "furniture")
        {
            type_form.append
            (
                `<div style="display: flex">
                    <div class="mb-3 col-md-1"><label for="height_cm" class="form-label">Height (CM)</label></div>
                    <div class="mb-3 col-md-3">
                        <input name="height_cm" type="text" class="form-control" id="height_cm" onkeyup="validateForm('height_cm');isNaturalNum('height_cm')">
                        <?php if (isset($errors["height_cm"])) : ?>
                            <span style="color: red"><?= $errors["height_cm"] ?></span>
                        <?php endif; ?>
                    </div>
                </div>`
            )
            type_form.append
            (
                `<div style="display: flex">
                    <div class="mb-3 col-md-1"><label for="width_cm" class="form-label">Width (CM)</label></div>
                    <div class="mb-3 col-md-3">
                        <input name="width_cm" type="text" class="form-control" id="width_cm" onkeyup="validateForm('width_cm');isNaturalNum('width_cm')">
                        <?php if (isset($errors["width_cm"])) : ?>
                            <span style="color: red"><?= $errors["height_cm"] ?></span>
                        <?php endif; ?>
                    </div>
                </div>`
            )
            type_form.append
            (
                `<div style="display: flex">
                        <div class="mb-3 col-md-1"><label for="length_cm" class="form-label">Length (CM)</label></div>
                        <div class="mb-3 col-md-3">
                            <input name="length_cm" type="text" class="form-control" id="length_cm" aria-describedby="furnHelp" onkeyup="validateForm('length_cm');isNaturalNum('length_cm')">
                            <?php if (isset($errors["length_cm"])) : ?>
                                <span style="color: red"><?= $errors["length_cm"] ?></span>
                            <?php endif; ?>
                        <div id="furnHelp" class="form-text">Please provide dimensions in HxWxL format</div>
                 </div></div>`
            )
        }
        else if (type === "book")
        {
            type_form.append
            (
                `<div style="display: flex">
                        <div class="mb-3 col-md-1"><label for="weight_kg" class="form-label">Weight (KG)</label></div>
                        <div class="mb-3 col-md-3">
                            <input name="weight_kg" type="text" class="form-control" id="weight_kg" aria-describedby="bookHelp" onkeyup="validateForm('weight_kg');isNaturalNum('weight_kg')">
                            <?php if (isset($errors["weight_kg"])) : ?>
                                <span style="color: red"><?= $errors["weight_kg"] ?></span>
                            <?php endif; ?>
                        <div id="bookHelp" class="form-text">Please provide weight in kilograms</div>
                 </div></div>`
            )
        }
    }
</script>

<script>
    window.onload = function()
    {
        document.getElementById('first_nav_item').onclick = function()
        {
            document.getElementById('product_form').submit();
            return false
        }
    }

    function displayErrors(element, field_id, error_message)
    {
        if (!element.hasClass("is-invalid"))
        {
            element.addClass("is-invalid")
        }

        if (!document.getElementById(`${field_id}-error`))
        {
            element.parent().append(`<div id="${field_id}-error" class="invalid-feedback">Please, submit required data</div>`)
        }
        else
        {
            $(`#${field_id}-error`).text(`${error_message}`)
        }
    }

    function isNaturalNum(field_id)
    {
        let element = $(`#${field_id}`)
        let check = /^\d+$/.test(element.val())

        if (!check)
        {
            if (element.val() !== "")
            {
                displayErrors(element, field_id, 'Please, provide the data of indicated type')
            }
        }
    }

    function validateForm(field_id)
    {
        let back_error = $(`#${field_id}-err`)

        if (back_error)
        {
            back_error.remove()
        }

        let element = $(`#${field_id}`)

        if (element.val() === "")
        {
            displayErrors(element, field_id, "Please, submit required data")
        }
        else
        {
            element.removeClass("is-invalid")
        }
    }
</script>