<!-- DISPLAY ERRORS IF THERE ARE ANY IN THE ARRAY -->
<?php if (count($errors) > 0) : ?>
    <div class="alert alert-primary">
        <?php foreach ($errors as $error) : ?>
            <p><?php echo $error?></p>
        <?php endforeach ?>
    </div>
<?php endif ?>
