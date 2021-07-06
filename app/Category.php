<?php

namespace App;

use App\Fields\Factory;
use App\Presenters\CategoryPresenter;
use App\Scopes\CategoryTenant;
use App\EncryptableModel;
use Laracasts\Presenter\PresentableTrait;

class Category extends EncryptableModel
{
    use PresentableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'icon', 'sort', 'fields', 'parent_id', 'owner_id'];

    /**
     * The attributes that are encrypted
     *
     * @var array
     */
    protected $encryptable = ['title', 'icon', 'fields'];

    /**
     * @var string
     */
    protected $presenter = CategoryPresenter::class;

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CategoryTenant);
    }

    /**
     * Relationship with Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Relationship with Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relationship with User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
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

        $data = $this->data;
        $data['fields'] = json_encode($value);
        $this->decryptedData = $data;
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
     * Returns all permissions referring to this resource
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function sharedWith()
    {
        return Permission::with('user')
            ->where('resource_type', Permission::RESOURCE_TYPE_CATEGORY)
            ->where('resource_id', $this->id)
            ->get();
    }

    /**
     * Returns all permissions granted for devices in this section
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function devicesSharedWith()
    {
        return Permission::with('user')
            ->where('resource_type', Permission::RESOURCE_TYPE_ITEM)
            ->whereIn('resource_id', $this->devices()->pluck('id'))
            ->get();
    }

    /**
     * Get all categories paged for admin
     *
     * @param false|array $ids
     * @return mixed
     */
    public static function pagedForAdmin($ids = false)
    {
        // todo sorting
        // $query = self::orderBy('sort')->orderBy('title');
        $query = self::orderBy('id');

        if (is_array($ids)) {
            $query->whereIn('id', $ids);
        }

        return $query->paginate(30);
    }

    /**
     * Get all categories ordered
     *
     * @return mixed
     */
    public static function getAll()
    {
        // todo sorting
        // return self::orderBy('sort')->orderBy('title')->get();

        return self::orderBy('id')->get();
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

    /**
     * Returns whether given user is owner of current resource
     *
     * @param User $user
     * @return bool
     */
    public function isOwner(User $user)
    {
        return $this->owner_id === $user->id;
    }
}
