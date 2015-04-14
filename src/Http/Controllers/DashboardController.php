<?php namespace Orchestra\Foundation\Http\Controllers;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Orchestra\Foundation\Processor\Account\ProfileDashboard as Processor;
use Orchestra\Contracts\Foundation\Listener\Account\ProfileDashboard as Listener;

class DashboardController extends AdminController implements Listener
{
    /**
     * Dashboard controller routing.
     *
     * @param \Orchestra\Foundation\Processor\Account\ProfileDashboard  $processor
     */
    public function __construct(Processor $processor)
    {
        $this->processor = $processor;

        parent::__construct();
    }

    /**
     * Setup controller filters.
     *
     * @return void
     */
    protected function setupFilters()
    {
        // User has to be authenticated before using this controller.
        $this->beforeFilter('orchestra.auth', ['only' => ['show']]);
    }

    /**
     * Show User Dashboard.
     *
     * GET (:orchestra)/
     *
     * @return mixed
     */
    public function show()
    {
        return $this->processor->show($this);
    }

    /**
     * Show missing pages.
     *
     * GET (:orchestra) return 404
     *
     * @return mixed
     */
    public function missing()
    {
        throw new NotFoundHttpException('Controller method not found.');
    }

    /**
     * Response to show dashboard.
     *
     * @param  array  $data
     *
     * @return mixed
     */
    public function showDashboard(array $data)
    {
        set_meta('title', trans('orchestra/foundation::title.home'));

        return view('orchestra/foundation::dashboard.index', $data);
    }
}
