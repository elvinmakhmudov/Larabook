<?php  namespace controllers\Traits; 

trait AjaxResponseTrait {

    /**
     * get Ajax Response for objects array
     *
     * @param $objects
     * @param $responseTemplate
     * @return array
     */
    public function getAjaxResponseFor($objects, $responseTemplate)
    {
        $new = [];
        foreach ($objects as $object)
        {
            $new[] = $this->getAjaxResponse($object, $responseTemplate);
        }

        return $new;
    }

    /**
     * Get ajax response for an object
     *
     * @param $object
     * @param $template
     * @return array
     */
    public function getAjaxResponse($object, $template)
    {
        $response = [];
        foreach ($template as $key => $value)
        {
            if ( is_array($value) )
            {
                $response[$key] = $this->getAjaxResponse($object[$key], $value);
            }
            else
            {
                $response[$value] = $object[$value];
            }
        }

        return $response;
    }
}