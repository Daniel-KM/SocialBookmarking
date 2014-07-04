<div id="socialBookmarkingServiceSettings">
    <div class="field">
        <div class="two columns alpha">
            <?php echo get_view()->formLabel(SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID, __('AddThis Account ID')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formText(
                SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID,
                get_option(SocialBookmarkingPlugin::ADDTHIS_ACCOUNT_ID),
                array('placeholder' => 'AddThisID')); ?>
            <p class="explanation"><?php echo __(
                'Enter an optional AddThis account ID to enable the tracking of shares via the AddThis analytics tool.'
            ); ?></p>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo get_view()->formLabel(SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION, __('Add to Header')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox(
                SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION,
                true,
                array('checked' => (boolean) get_option(SocialBookmarkingPlugin::ADD_TO_HEADER_OPTION))); ?>
            <p class="explanation"><?php echo __(
                'If checked, this plugin will add a social bookmarking toolbar in the header of every page.'
            ); ?></p>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo get_view()->formLabel(SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION, __('Add to Items')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox(
                SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION,
                true,
                array('checked' => (boolean) get_option(SocialBookmarkingPlugin::ADD_TO_OMEKA_ITEMS_OPTION))); ?>
            <p class="explanation"><?php echo __(
                'If checked, this plugin will add a social bookmarking toolbar at the bottom of every public item show page.'
            ); ?></p>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <?php echo get_view()->formLabel(SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION, __('Add to Collections')); ?>
        </div>
        <div class="inputs five columns omega">
            <?php echo get_view()->formCheckbox(
                SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION,
                true,
                array('checked' => (boolean) get_option(SocialBookmarkingPlugin::ADD_TO_OMEKA_COLLECTIONS_OPTION))); ?>
            <p class="explanation"><?php echo __(
                'If checked, this plugin will add a social bookmarking toolbar at the bottom of every public collection show page.'
            ); ?></p>
        </div>
    </div>

    <div class="field">
        <div class="two columns alpha">
            <p><?php echo __('Choose which social bookmarking services you would like to use on your site.'); ?></p>
        </div>
        <div class="inputs five columns omega">
            <ul class="details">
            <?php foreach($services as $serviceCode => $serviceInfo): ?>
                <li class="details">
                    <?php echo get_view()->formCheckbox($serviceCode, true, array('checked' => (boolean) $setServices[$serviceCode])); ?>
                    <img src="<?php echo html_escape($serviceInfo['icon']); ?>" /> <?php echo html_escape($serviceInfo['name']); ?>
                </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
