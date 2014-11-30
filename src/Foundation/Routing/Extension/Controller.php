<?php namespace Orchestra\Foundation\Routing\Extension;

use Illuminate\Support\Fluent;
use Orchestra\Foundation\Routing\AdminController;
use Orchestra\Contracts\Extension\Listener\Extension;

class Controller extends AdminController implements Extension
{
    /**
     * Extension processor implementation.
     *
     * @var \Orchestra\Extension\Processor\Processor
     */
    protected $processor;

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupFilters()
    {
        $this->beforeFilter('orchestra.auth');
        $this->beforeFilter('orchestra.manage');
        $this->beforeFilter('orchestra.csrf');
    }

    /**
     * Abort request when extension requirement mismatched.
     *
     * @return mixed
     */
    public function abortWhenRequirementMismatched()
    {
        return $this->suspend(404);
    }

    /**
     * Get extension information.
     *
     * @param  string  $uid
     * @return \Illuminate\Support\Fluent
     */
    protected function getExtension($uid)
    {
        $name = str_replace('.', '/', $uid);

        return new Fluent(['name' => $name, 'uid' => $uid]);
    }
}
