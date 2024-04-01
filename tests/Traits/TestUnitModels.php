<?php

namespace Tests\Traits;

trait TestUnitModels
{
    /**
     * The contract model to be tested.
     *
     * @return string
     */
    abstract protected function model(): string;

    /**
     * The contract fillable attributes that should be tested.
     *
     * @return void
     */
    abstract public function test_fillable(): void;

    /**
     * The contract dates attributes that should be tested.
     *
     * @return void
     */
    abstract public function test_dates_attribute(): void;

    /**
     * The contract casts attributes that should be tested.
     *
     * @return void
     */
    abstract public function test_casts_attribute(): void;

    /**
     * Test if the fillable attributes are correctly.
     *
     * @param array $fillable
     * @return bool
     */
    public function verifyIfExistFillable(array $fillable): void
    {
        $model = $this->model();

        $this->assertEquals($fillable, (new $model())->getFillable());
    }

    /**
     * Test if the model uses the correctly traits.
     *
     * @param array $traits
     * @return void
     */
    public function verifyIfUseTraits(array $traits): void
    {
        $modelTraits = array_keys(class_uses($this->model()));

        $this->assertEquals($traits, $modelTraits);
    }

    /**
     * Test if the dates attributes are correctly.
     *
     * @param array $dates
     * @return void
     */
    public function verifyDates(array $dates): void
    {
        $model = $this->model();
        $model = (new $model());

        collect($dates)->map(function (string $date) use ($model) {
            $this->assertContains($date, $model->getDates());
        });

        $this->assertCount(count($dates), $model->getDates());
    }

    /**
     * Test if the casts attributes are correctly.
     *
     * @param array $casts
     * @return void
     */
    public function verifyCasts(array $casts): void
    {
        $model = $this->model();

        $model = (new $model());

        $this->assertEquals($casts, $model->getCasts());
    }
}
