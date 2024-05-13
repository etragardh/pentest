<?php

namespace Breakdance\Forms\Actions;

class ActionProvider
{

    use \Breakdance\Singleton;

    /**
     * Get the actions that should be available for the builder.
     * @var Action[]|ApiAction[]
     */
    public $actions = [];

    /**
     * Get the actions dropdown path.
     * @return string
     */
    public $actionsPath = 'content.actions.actions';

    public function __construct()
    {
        $this->actions = [
            // free actions should be at the top for better UX
            new \Breakdance\Forms\Actions\StoreSubmission(),
            new \Breakdance\Forms\Actions\ActiveCampaign(),
            new \Breakdance\Forms\Actions\CustomJavaScript(),
            new \Breakdance\Forms\Actions\ConvertKit(),
            new \Breakdance\Forms\Actions\Drip(),
            new \Breakdance\Forms\Actions\Discord(),
            new \Breakdance\Forms\Actions\Slack(),
            new \Breakdance\Forms\Actions\Email(),
            new \Breakdance\Forms\Actions\GetResponse(),
            new \Breakdance\Forms\Actions\MailChimp(),
            new \Breakdance\Forms\Actions\MailerLite(),
            new \Breakdance\Forms\Actions\Webhook(),
            new \Breakdance\Forms\Actions\Popup(),
        ];
    }

    /**
     * @param class-string<Action> $action
     */
    public function registerAction($action)
    {
        $this->actions[] = new $action();
    }

    /**
     * Get a list of actions instances
     * @return Action[]
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param string $slug
     * @return Action|null
     */
    public function getActionBySlug($slug)
    {
        foreach ($this->actions as $action) {
            if ($slug == $action->slug()) {
                return $action;
            }
        }

        return null;
    }

    /**
     * Get a list of all controls for each action
     * @return Control[]
     */
    public function getControls()
    {
        $actions = array_filter($this->actions, function (Action $action) {
            return count($action->controls());
        });

        return array_map(function (Action $action) {
            return \Breakdance\Elements\controlSection(
                $action::slug(),
                $action::name(),
                $action->controls(),
                [
                    'condition' => [
                        'path' => $this->actionsPath,
                        'operand' => 'includes',
                        'value' => $action::slug()
                    ]
                ],
                'modal'
            );
        }, $actions);
    }

}

function registerAction(Action $action) {
    ActionProvider::getInstance()->actions[] = $action;
}
