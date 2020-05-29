<div class="theme-component search-form-default">

    <form role="search" method="get" class="search-form" action="/">
        <label class="search-label"><i class="fa fa-search"></i></label>

        <fieldset class="search-field">

            <input type="search" class="search-field" value="" name="s" />

            <button type="submit" class="search-submit">
                <i class="fa fa-search"></i>
            </button>

            <?php if (defined('ICL_LANGUAGE_CODE')): // WPML Search Language ?>
                <input type='hidden' name='lang' value='<?php echo ICL_LANGUAGE_CODE; ?>'/>
            <?php endif; ?>

        </fieldset>
    </form>

</div>
