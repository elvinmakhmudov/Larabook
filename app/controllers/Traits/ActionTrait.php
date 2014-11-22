<?php  namespace controllers\Traits; 

use Illuminate\Support\Facades\Response;

trait ActionTrait {

    /**
     * Action that need to be done
     *
     * @param $action
     * @param null $allowedMethods
     * @return \Illuminate\Http\Response
     */
    public function doAction($action, $allowedMethods = null)
    {
        if( ! in_array($action, $allowedMethods) || ! method_exists($this, $action) )
        {
            return Response::make('not found', 404);
        }

        return $this->$action();
    }
}