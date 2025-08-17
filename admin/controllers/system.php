<?php

$cfg = get_all_config();

?>

<style>



</style>


<div class="card shadow mb-4">
    <div class="card-body">
        <form id="form-setting">
            <?php

            foreach ($cfg as $c):

            ?>

                <?php


                if (in_array($c["value_type"], ["text", "password", "number", "date"])) {
                ?>
                    <div class="form-group">
                        <label for=""><?= $c["config_display_name"] ?></label>
                        <input type="<?= $c["value_type"] ?>" step='0.1' value='<?= $c["value"] ?>'
                            class="form-control" name="<?= $c["name"] ?>">
                    </div>
                <?php
                }

                if (in_array($c["value_type"], ["bool"])) {
                ?>
                    <div class="form-check">
                        <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="<?= $c["name"] ?>" <?= $c["value"] == 1 ? "checked" : "" ?>>
                            <?= $c["config_display_name"] ?>
                        </label>
                    </div>
                <?php
                }

                if (in_array($c["value_type"], ["textarea"])) {
                ?>
                    <div class="form-group">
                        <label><?= $c["config_display_name"] ?></label>
                        <textarea class="form-control" nor id="<?= $c["name"] ?>" name="<?= $c["name"] ?>"><?=$c["value"]?></textarea>
                    </div>
                <?php
                }

                if (in_array($c["value_type"], ["html"])) {
                    ?>
                        <div class="form-group">
                            <label><?= $c["config_display_name"] ?></label>
                            <textarea hah id="<?= $c["name"] ?>" name="<?= $c["name"] ?>"><?=$c["value"]?></textarea>
                        </div>
                        <?= "<script> CKEDITOR.replace('".$c["name"]."'); </script>" ?>
                    <?php
                    }

                ?>


            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary w-100 mt-2">Lưu</button>
        </form>
    </div>
</div>


<?php admin_footer(); ?>

<script>
    $("#form-setting").on("submit", function(e) {
        e.preventDefault();

        var data = {};

        data.action_type = "edit_configs";

        // $(this).serializeArray().forEach(element => {
        //     console.log(element);
        //     data[element.name] = element.value;
        // });

        // $('input[type="checkbox"]:not(:checked)').each(function() {
        //     if ($.inArray(this.name, names) === -1) {
        //         arr.push({
        //             name: this.name,
        //             value: 'off'
        //         });
        //     }
        // });

        var data = {};

        $(this).serializeArray().forEach(element => {
            data[element.name] = element.value;
        });

        $('input[type="checkbox"]').each(function() {
            // if ($.inArray(this.name, names) === -1) {
            //     arr.push({
            //         name: this.name,
            //         value: ($(this).is(":checked") ? 1 : 0) ?? 0
            //     });

            //     arr[this.name] = ($(this).is(":checked") ? 1 : 0) ?? 0

            // }
            data[this.name] = ($(this).is(":checked") ? 1 : 0) ?? 0
        });

        $('textarea[hah]').each(function() {
            // if ($.inArray(this.name, names) === -1) {
            //     arr.push({
            //         name: this.name,
            //         value: ($(this).is(":checked") ? 1 : 0) ?? 0
            //     });

            //     arr[this.name] = ($(this).is(":checked") ? 1 : 0) ?? 0

            // }

            data[this.name] = CKEDITOR.instances[this.name].getData();
        });

        $('textarea[nor]').each(function() {
            data[this.name] = $(this).val();
        });

        api({
            data: {
                action_type: "update_config",
                data
            },
        });
    })
</script>