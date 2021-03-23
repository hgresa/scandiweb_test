<?php $chunked_array = (array_chunk($sql, 4)); ?>

<?php foreach ($chunked_array as $array) : ?>
    <div class="row" style="margin-bottom: 40px;margin-left: 0px">
        <?php foreach ($array as $product_item) : ?>
            <?php $product_specific_unit = array_values($units[$product_item["type"]])[0] ?>
            <?php $product_specific_measure = array_key_first($units[$product_item["type"]]) ?>
            <div class="card w-25 text-center" style="margin-right: 40px; width: 21%!important">
                <input id="<?= $product_item["sku"] ?>" type="checkbox" class="form-check">
                <div class="card-body">
                    <p class="card-text"><?= $product_item["sku"] ?></p>
                    <p class="card-text"><?= $product_item["name"] ?></p>
                    <p class="card-text"><?= $product_item["price"] ?> $</p>
                    <p class="card-text"><?= $product_specific_measure.": ".$product_item["measure_value"]." ".$product_specific_unit ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endforeach; ?>
