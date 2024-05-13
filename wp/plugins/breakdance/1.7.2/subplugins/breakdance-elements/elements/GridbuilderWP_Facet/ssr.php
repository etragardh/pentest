<?php

/**
 * @var array $propertiesData
 */
$facet = $propertiesData['content']['facet']['facet'] ?? null;

if (\Breakdance\isRequestFromBuilderSsr()){
    $facets = \Breakdance\Integrations\WPGB\getWpgbFacets();

    $currentFacetIndex = array_search($facet, array_column($facets, 'value'));
    $currentFacet = $currentFacetIndex === false ? false : $facets[$currentFacetIndex];

    ?>
    <div class="bde-wpgbfacet-builder">
        WPGB's <?= $currentFacet ? '"'.$currentFacet['text'].'" facet' : ""?> facet will appear here on the frontend.
    </div>
    <?php
} else {
    echo do_shortcode("[wpgb_facet id='$facet' grid='wpgb-content']");
}
