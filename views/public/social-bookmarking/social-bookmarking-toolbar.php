<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style" addthis:url="<?php
    echo html_escape($url); ?>" addthis:title="<?php echo html_escape($title); ?>" addthis:description="<?php echo html_escape($description); ?>">
<?php
    $booleanFilter = new Omeka_Filter_Boolean;
    foreach ($serviceSettings as $serviceCode => $value) :
        if ($booleanFilter->filter($value) && array_key_exists($serviceCode, $services)) : ?>
    <a class="addthis_button_<?php echo html_escape($serviceCode); ?>"></a>
        <?php endif;
    endforeach;
?>
    <a class="addthis_button_compact"></a>
    <a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">var addthis_config = { ui_508_compliant: true };</script>
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js<?php echo empty($addthisAccountID) ? '' : '#pubid=' . $addthisAccountID; ?>"></script>
<!-- AddThis Button END -->
