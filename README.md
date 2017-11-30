# Laravel Meta Fields

Laravel meta fields attempts to bring the powerful concept of the popular WordPress plugin advanced custom fields into Laravel for quick rich content and management form field creation. Meta field schemas are attached to models and provide rich content fields such as text, dropdown, wysiwyg which can be managed and searched. The idea is not to replace model properties but to extend models with customisable content focused meta values using a simplied meta table of key value pairs.

## Quickstart

The model

```

namespace Example\Person\Model;

use Sackrin\Meta\Model\HasMetaFields;
use Sackrin\Meta\Model\MetaModel;
use Sackrin\Meta\Field\Blueprint;
use Sackrin\Meta\Field\Type\Group;
use Sackrin\Meta\Field\Type\Repeater;
use Sackrin\Meta\Field\Type\Text;


class Person extends Model implements MetaModel {

    use HasMetaFields;

    protected $table = 'people';

    /**
     * Indicates which model is responsible for this model's meta fields
     */
    public static $metaModel = PersonMeta::class;

    /**
     * Provide the meta fields blueprint
     * This object should contain all of the meta fields for this model
     */
    public static function fieldMetaBlueprint() {
        // Return the build field schema object
        return (new Blueprint())
            ->addBlueprint((new Text('company')))
            ->addBlueprint((new Group('details'))
                ->addBlueprint((new Text('first_name')))
                ->addBlueprint((new Text('surname')))
                ->addBlueprint((new Repeater('phones'))
                    ->addBlueprint((new Text('phone_number')))
                    ->addBlueprint((new Text('phone_area')))
                )
                ->addBlueprint((new Repeater('emails'))
                    ->addBlueprint((new Text('emailaddress')))
                )
            );
    }

}

```

```
    // Add to a migration to create the model table
    Schema::create('people', function (Blueprint $table) {
        $table->increments('id');
        $table->timestamps();
    });
    
```

Supporting meta model. Each model should have it's own meta model

```

namespace Example\Person\Model;

use Illuminate\Database\Eloquent\Model;

class PersonMeta extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['machine', 'reference', 'path', 'position', 'type', 'value'];

    /**
     * Get the author of the post.
     */
    public function person()
    {
        return $this->belongsTo('Example\Person\Model\Person');
    }

}

```

```
    // Add to a migration to create the model meta table
    Schema::create('people_meta', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('people_id')->unsigned();
        $table->foreign('people_id')->references('id')->on('tutelage_activities');
        $table->text('machine');
        $table->text('reference');
        $table->text('path');
        $table->integer('position');
        $table->string('type');
        $table->longText('value')->nullable();
        $table->timestamps();
    });
    
```

Set field

```
// Create or retrieve a person record then...
$person->setField('company', 'Acme Co.');
$person->save();

```

Get field

```
// Create or retrieve a person record then...
$person->getField('company');

```