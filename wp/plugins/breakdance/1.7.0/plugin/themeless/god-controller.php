<?php

namespace Breakdance\Themeless;

use function Breakdance\Render\getCircularRenderErrorAsHtml;
use function Breakdance\Util\ErrorHTML\wrapWithError;

class ThemelessController
{

    use \Breakdance\Singleton;

    /** @var Template[] */
    public $templates;

    /** @var Template[] */
    public $headers;

    /** @var Template[] */
    public $footers;

    /** @var Template[] */
    public $popups;

    /** @var TemplateCondition[] */
    public $conditions = [];

    /** @var array<string,TemplateTypeCategory> */
    public $templateTypeCategories = [];

    public function __construct()
    {
        $this->templates = getTemplatesFromDb();
        $this->headers = getHeadersFromDb();
        $this->footers = getFootersFromDb();
        $this->popups = getPopupsFromDb();
    }

    /**
     * @param TemplateCondition $condition
     * @return void
     */
    public function registerCondition($condition)
    {
        /* todo: avoid duplicate slugs */
        $this->conditions[] = $condition;
    }

    /**
     * @param string $categoryName
     * @return void
     */
    public function registerTemplateTypeCategory($categoryName)
    {
        if (!array_key_exists($categoryName, $this->templateTypeCategories)) {
            $this->templateTypeCategories[$categoryName] = [
                'label' => $categoryName,
                'types' => [],
            ];
        }
    }

    /**
     * @param string $categoryName
     * @param TemplateType $templateType
     * @return void
     */
    public function registerTemplateType($categoryName, $templateType)
    {

        if (!array_key_exists($categoryName, $this->templateTypeCategories)) {
            $this->templateTypeCategories[$categoryName] = [
                'label' => $categoryName,
                'types' => [$templateType],
            ];
        } else {
            $this->templateTypeCategories[$categoryName]['types'][] = $templateType;
        }
    }

    // disorganized stuff */

    /** @var null|int[] */
    public $templateHierarchyForRequest = null;

    /**
     * @var null|int[]
     *
     * for debugging reasons the user might want to know the template hierarchy for request,
     * so we might want this info for use in the admin menu, etc.
     * since templateHierarchyForRequest gets popped down to nothing when rendering,
     * we save a copy of it here
     */
    public $originalTemplateHierarchyForRequest = null;

    /**
     * @param int $templateId
     * @return void
     */
    public function buildTemplateHierarchyForRequest($templateId)
    {

        $this->templateHierarchyForRequest = $this->getTemplateHierarchy($templateId);
        $this->originalTemplateHierarchyForRequest = $this->templateHierarchyForRequest;
    }

    /**
     * @param int $templateId
     * @return int[]
     */
    private function getTemplateHierarchy($templateId)
    {

        /**
         * @var int[]
         */
        $hierarchy = [];

        /**
         * @var int[]
         */
        $templateStack = [];

        while (true) {
            $hierarchy[] = $templateId;
            $settings = getTemplateSettingsFromDatabase($templateId);

            if (is_array($settings) && isset($settings['parentId'])) {
                $templateId = $settings['parentId'];
            } else {
                break;
            }

            if (in_array($templateId, $templateStack)) {
                // the first place we'd catch a circular template reference is here. we'd never even get to the CircularRendererTracker
                // as used in the render function. that thing is really for global blocks - not templates
                // but, the error messages it puts out look great and just take an array of IDs that result in a circular loop
                // so we'll re-use that here

                echo getCircularRenderErrorAsHtml(array_merge($templateStack, [$templateId]));
                die();

                // lol, echo and die is not the right way to do things.
            }

            $templateStack[] = $templateId;

            if ($templateId === 0) {
                break;
            }
        }

        return $hierarchy;
    }

    /**
     * @return int|false
     */
    public function popHierarchy()
    {
        if (!$this->templateHierarchyForRequest) {
            return false;
        }

        $postId = array_pop($this->templateHierarchyForRequest);

        /*
        if the user has Breakdance permissions, display errors if the
        template hierarchy is broken

        to cause a broken hierarchy, create Template A, then create
        Template B which inherits from Template A, and then deletes
        Template A.

        when a post which Template B applies to is rendered, or you
        try to edit Template B in Breakdance, this error will appear
        */

        $permission = \Breakdance\Permissions\getUserPermission();
        if ($permission && $permission['slug'] === 'full') {
            // TODO: check at $this->templates instead for performance reasons

            /** @var \WP_Post|null */
            $post = get_post($postId);
            if (!$post || $post->post_status === 'trash') {
                echo missingTemplateError(
                    $this->originalTemplateHierarchyForRequest
                        ? $this->originalTemplateHierarchyForRequest
                        : [],
                    $postId,
                    $post && $post->post_status === 'trash'
                );
                return false;
            }
        }

        return $postId;
    }

    /**
     * @return Template[]
     */
    public function getAllTemplates()
    {
        return array_merge(
            $this->templates,
            $this->headers,
            $this->footers,
            $this->popups,
        );
    }

    /**
     * @param string $postType
     * @return TemplateCondition[]
     */
    public function getConditionsForPostType($postType)
    {
        return array_values(array_filter($this->conditions, static function($condition) use ($postType) {
            if (!array_key_exists('availableForPostType', $condition) || !is_array($condition['availableForPostType'])) {
                return true;
            }
            return in_array($postType, $condition['availableForPostType']);
        }));
    }

    /**
     * @param string $slug
     * @return TemplateCondition|false
     */
    public function getConditionBySlug($slug)
    {
        $conditions = array_filter(
            $this->conditions,
            static function ($condition) use ($slug) {
                return $condition['slug'] === $slug;
            }
        );

        $condition = array_pop($conditions);

        return $condition ?: false;
    }

}


/**
 * @param int[] $originalTemplateHierarchyForRequest
 * @param int $missingPostId
 * @param bool $missingPostIsTrashed
 * @return string
 */
function missingTemplateError($originalTemplateHierarchyForRequest, $missingPostId, $missingPostIsTrashed) {

    ob_start();

    echo "<h4>Template Hierarchy Error</h4>";

    echo 'There is no template with ID '. $missingPostId.'.<br /><br />';

    for ($i = 0; $i < count($originalTemplateHierarchyForRequest); $i++) {
        $postId = $originalTemplateHierarchyForRequest[$i];
        if ($originalTemplateHierarchyForRequest[$i + 1] === $missingPostId) {

            $postTitle = get_the_title($postId);

            $manageTemplatesUrl = admin_url('admin.php?page=' . BREAKDANCE_TEMPLATE_POST_TYPE);

            $impossibleBecause = $missingPostIsTrashed ? "has been moved to trash" : "doesn't exist";
            echo "But template <em>{$postTitle} (ID {$postId})</em> tries to inherit its design from template {$missingPostId}. This is impossible, since that template {$impossibleBecause}.<br /><br />";
            echo "To fix, edit template <em>{$postTitle} (ID {$postId})</em>, and clear the <i>Inherit Design From</i> field or choose a template that exists.<br /><br />";
            echo "<a force-allow-clicks target='_blank' href='{$manageTemplatesUrl}'>Manage Templates</a>";

        }

    }

    return wrapWithError(ob_get_clean());
}


