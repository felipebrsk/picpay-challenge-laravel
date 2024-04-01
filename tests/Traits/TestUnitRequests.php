<?php

namespace Tests\Traits;

trait TestUnitRequests
{
    /**
     * The testable rules.
     *
     * @var array
     */
    private $rules;

    /**
     * The testable validator.
     *
     * @var object
     */
    private $validator;

    /**
     * Get the testable request.
     *
     * @return string
     */
    abstract protected function request(): string;

    /**
     * Set up operations
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $request = $this->request();
        $this->rules = (new $request())->rules();
        $this->validator = $this->app['validator'];
    }

    /**
     * Check a field and value against validation rule
     *
     * @param array $fields
     * @return bool
     */
    public function validateFields(array $fields): bool
    {
        $validationData = [];
        $validationRules = [];

        foreach ($fields as $field => $value) {
            $validationData[$field] = $value;
            $validationRules[$field] = $this->rules[$field];
        }

        return $this->validator->make($validationData, $validationRules)->passes();
    }
}
