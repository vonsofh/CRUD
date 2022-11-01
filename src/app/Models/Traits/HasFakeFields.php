<?php

namespace Backpack\CRUD\app\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Traversable;

/*
|--------------------------------------------------------------------------
| Methods for Fake Fields functionality (used in PageManager).
|--------------------------------------------------------------------------
*/
trait HasFakeFields
{

    /**
     * Add fake fields as regular attributes, even though they are stored as JSON.
     *
     * @param  array  $columns  - the database columns that contain the JSONs
     */
    public function addFakes(array $columns = [])
    {
        $columns = ! empty($columns) ? $columns : $this->getFakeColumns();

        foreach ($columns as $key => $column) {
            if (! isset($this->attributes[$column])) {
                continue;
            }

            $column_contents = $this->{$column};

            if ($this->shouldDecodeFake($column)) {
                $column_contents = json_decode($column_contents);
            }

            if ((is_array($column_contents) || is_object($column_contents) || $column_contents instanceof Traversable)) {
                foreach ($column_contents as $fake_field_name => $fake_field_value) {
                    $this->setAttribute($fake_field_name, $fake_field_value);
                }
            }
        }
    }

    /**
     * Return the model fake columns
     *
     * @return array
     */
    public function getFakeColumns() {
        return $this->fakeColumns ?? ['extras'];
    }

    /**
     * Return the entity with fake fields as attributes.
     *
     * @param  array  $columns  - the database columns that contain the JSONs
     * @return Model
     */
    public function withFakes(array $columns = [])
    {
        $columns = ! empty($columns) ? $columns : $this->getFakeColumns();

        $this->addFakes($columns);

        return $this;
    }

    /**
     * Determine if this fake column should be json_decoded.
     *
     * @param string $column fake column name
     * @return bool
     */
    public function shouldDecodeFake(string $column)
    {
        return ! in_array($column, array_keys($this->casts));
    }

    /**
     * Determine if this fake column should get json_encoded or not.
     *
     * @param string $column fake column name
     * @return bool
     */
    public function shouldEncodeFake(string $column)
    {
        return ! in_array($column, array_keys($this->casts));
    }

    /**
     * Check if the given column name is a fakeColumn.
     *
     * @param string $column
     * @return bool
     */
    public function isFakeColumn(string $column)
    {
        return in_array($column, $this->getFakeColumns());
    }
}
