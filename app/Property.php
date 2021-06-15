<?php
namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\User;

/**
 * Class Property
 *
 * @package App
 * @property string $name
 * @property string $address
 * @property string $photo
*/
class Property extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'address', 'photo', 'user_id'];

    public function inquilinos()
      {
        $result=$this->belongsTo('App\User', 'id', 'property_id');
        return $result;
        //return count($result);
      }

    public function numero_inquilinos($idproperty)
      {
        $result=User::where("property_id","=",$idproperty)->get();
         return count($result);
      }
    
    
    
}
