<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'code',
        'name',
        'contact_person',
        'dial_code',
        'phone_number',
        'email',
        'status',
        'address_line_1',
        'address_line_2',
        'country',
        'state',
        'city',
        'latitude',
        'longitude',
        'location_url',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Get the customer that owns the location.
     */
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Get the country.
     */
    public function countryr()
    {
        return $this->belongsTo(Country::class, 'country');
    }

    /**
     * Get the state.
     */
    public function stater()
    {
        return $this->belongsTo(State::class, 'state');
    }

    /**
     * Get the city.
     */
    public function cityr()
    {
        return $this->belongsTo(City::class, 'city');
    }
}
