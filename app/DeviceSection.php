<?php

namespace App;

use App\Fields\Factory;
use App\Presenters\DeviceSectionPresenter;
use Illuminate\Database\Eloquent\Model;
use Laracasts\Presenter\PresentableTrait;

class DeviceSection extends Model
{
    use PresentableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'icon', 'sort', 'fields', 'categories'];

    /**
     * @var string
     */
    protected $presenter = DeviceSectionPresenter::class;

    /**
     * Auto encode the data field
     *
     * @param string $value
     */
    public function setCategoriesAttribute($value)
    {
        $this->attributes['categories'] = json_encode($value);
    }

    /**
     * Decode the data field
     *
     * @param string $value
     * @return array
     */
    public function getCategoriesAttribute($value)
    {
        $return = @json_decode($value, true);

        return is_array($return) ? $return : [];
    }

    /**
     * Relationship with DeviceSection
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function devices()
    {
        return $this->hasMany('App\Device', 'section_id');
    }

    /**
     * Auto encode the fields field
     *
     * @param string $value
     */
    public function setFieldsAttribute($value)
    {
        if (!is_array($value)) $value = [];

        $value = array_merge($value, []); // reset array keys

        $this->attributes['fields'] = json_encode($value);
    }

    /**
     * Decode the fields field
     *
     * @param string $value
     * @return array
     */
    public function getFieldsAttribute($value)
    {
        if (is_null($value)) {
            return null;
        }

        $items = @json_decode($value, true);

        if (!is_array($items)) return [];

        $return = [];

        foreach ($items as $item) {
            try {
                $options = isset($item['options']) ? $item['options']: [];

                $return[] = Factory::generate(
                    $item['key'],
                    $item['name'],
                    $item['type'],
                    $options
                );
            }
            catch (\Exception $e) {}
        }

        return $return;
    }

    /**
     * Get all device sections paged for admin
     *
     * @param false|array $ids
     * @return mixed
     */
    public static function pagedForAdmin($ids)
    {
        $query = self::orderBy('sort')->orderBy('title');

        if (is_array($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->paginate(30);
    }

    /**
     * Get all device sections ordered
     *
     * @param $ids
     * @return mixed
     */
    public static function getAll($ids)
    {
        $query = self::orderBy('sort')->orderBy('title');

        if (is_array($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->get();
    }

    /**
     * Get all field names
     *
     * @return array
     */
    public function getFieldsForSelect()
    {
        $return = [];

        foreach ($this->fields as $field) {
            $return[$field->getInputName()] = $field->getName();
        }

        return $return;
    }
}
