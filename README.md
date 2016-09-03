# Intracto DataTables library

Handle AJAX requests for Datatables.net.

## Install

```
composer require intracto/datatables-backend

or

"intracto/datatables-backend" : "dev-master"
```


### Columns
 
Container class to hold `Column` objects. This is used to get the field for sorting.

### Column

Hold data about a column, can be used for the frontend to render `<th>` and let DataTables known which fields
are sortable and searchable (via javascript).

### DataProvider

Get the data to show in the datatable. Requires `Parameters`, `DataTablesRepository`, `ColumnTransformer`.

### Parameters

Container class to hold data from the datatables AJAX request.

### DataTablesRepositoryInterface

Defines query functions needed to fetch the data.

### DataTablesRepositoryTrait

An implementation of `DataTablesRepositoryInterface` with general queries. Only available for Doctrine ODM for now.

### ColumnTransformerInterface

Transform data fetched from the `Repository` to format needed for the datatables. The order of the fields is important here.

---

## Example

### Columns

```
class AddressListColumns extends Columns
{
    public function __construct()
    {
        parent::__construct(
            array(
                // In order of the tables headers
                new Column('city', 'city', true, true),
                new Column('zip', 'zip', false, false),
                new Column('street', 'street', false, false),
                new Column('actions', 'actions', false, false),
            )
        );
    }
}
```

### Transformer

```
class AddressListColumnTransformer implements ColumnTransformerInterface
{
    /**
     * @var EngineInterface
     */
    private $renderEngine;

    /**
     * AddressListColumnTransformer constructor
     *
     * @param EngineInterface $renderEngine
     */
    public function __construct(EngineInterface $renderEngine)
    {
        $this->renderEngine = $renderEngine;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function transform(array $data)
    {
        $columns = array();

        foreach ($data as $address) {
            /**
             * @var Address $address
             */
            $columns[] = array(
                $address->getCity()
                $address->getZip()
                $address->getStreet()
                $this->renderEngine->render('Address/_list.actions.html.twig', array('address' => $address)),
            );
        }

        return $columns;
    }
}
```

### Repository

```
class AddressRepository extends DocumentRepository implements DataTablesRepositoryInterface
{
    use DataTablesRepositoryTrait;
}
```

### Controller

```
public function listAction()
{
    $columns = new AddressListColumns();

    return array(
        'columns' => $columns,
    );
}

public function ajaxListAction(Request $request)
{
    $parameters = Parameters::fromParameterBag($request->query, new AddressListColumns());

    $data = $this->dataTablesDataProvider->getData(
        $parameters,
        $addressRepository,
        $addressListColumnTransformer
    );

    return new JsonResponse($data);
}
```

---

### Frontend

#### External JS

Include `ajax-datatables.js` after loading the official datatables.js plug-in

#### Twig html

Make sure the table has the "ajaxdatatable" class
Loop all the columns in the table head

```
<table class="ajaxdatatable">
    <thead>
        <tr>
            {% for column in columns %}
                <th>{{ ("translatable.prefix." ~ column.name)|trans }}</th>
            {% endfor %}
        </tr>
    </thead>
</table>
```

Default sorting needed? Add the class `default_sort` and `asc`|`desc` to the `<th>` to the correct column

```
<th {% if column.name == 'email' %} class="default_sort desc"{% endif %}>
    ...
</th>
```


#### Twig JS

On the twig page where the ajax datatable must be loaded, place following script block

The `filters` are optional, here you can pass searchable/filterable fields

```
<script>
    $(document).ready(function(){

        {# define the ajax call path #}
        var ajaxCallPath = "{{ path("path_to_your_ajax_call") }}";

        {# define which columns are orderable #}
        var sortable = [
            {% for column in columns %}
                {%- if column.orderable -%}
                    {{ 'null' }}
                {%- else -%}
                    {{ '{ "orderable": false }'}}
                {%- endif -%}
                {{ ',' }}
            {% endfor %}
        ];

        {# page load filter fields, do not add .val() since we need the reference #}
        var filters = {
            'name': $("#js-filter-naam"),
            'address.city': $("#js-filter-city")
        };

        {# on page load, init the datatable #}
        ajaxDatatable(ajaxCallPath, sortable, filters);

        {# on submit, change, whenever you want #}
        $(document).on("click", "#js-filter-submit", function(){
            {# no need to pass the parameters again #}
            ajaxDatatable();
        });
    });
</script>
```
