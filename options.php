<?php

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

?>

<div class="wrap">
    <h2>Wrappic connect</h2>

    <form method="post" action="options.php">
        <?php wp_nonce_field('update-options'); ?>

        <table class="form-table">

        <tr valign="top">
            <th scope="row">Api token</th>
            <td><input type="text" name="api_token" value="<?php echo get_option('api_token'); ?>" /></td>
        </tr>

        </table>

        <input type="hidden" name="action" value="update" />
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Opslaan') ?>" />
        </p>
    </form>
</div>