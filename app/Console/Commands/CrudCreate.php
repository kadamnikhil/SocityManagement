<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CrudCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:create {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');
        $crud = \App\Models\Crud::find($id);
        
        $fields = [];
        foreach($crud->fields as $key => $field){
            $fields[$field['field']] = $field['fieldType'];
        }

        $validations = [];
        foreach($crud->validations as $key => $validation){
            $validations[$validation['validationField']] = $validation['validationRule'];
        }

        $messages = [];
        foreach($crud->messages as $key => $message){
            $messages[$message['validationField']] = $message['validationMessage'];
        }

        $data = [
            'table_name' => $crud->table_name,
            'model_name' => $crud->model_name,
            'singular_name' => $crud->singular_name,
            'controller_name' => $crud->controller_name,
            'route_name' => $crud->route_name,
            'view_folder_name' => $crud->view_folder_name,
            'fields' => $fields,
            'validations' => $validations,
            'messages' => $messages,
        ];

        // $data = [
    //         'table_name' => 'students',
    //         'model_name' => 'Student',
    //         'singular_name' => 'student',
    //         'controller_name' => 'StudentController',
    //         'route_name' => 'students',
    //         'view_folder_name' => 'Students',
    //         'fields' => [
    //             'first_name' => 'INPUT',
    //             'last_name' => 'INPUT',
    //             'email' => 'INPUT',
    //             'phone' => 'INPUT',
    //             'address' => 'TEXTAREA',
    //             'gender' => 'SELECT',
    //             'status' => 'SELECT'
    //         ],
    //         'validations' => [
    //             'name' => 'required',
    //             'email' => 'required|email',
    //             'phone' => 'required',
    //             'address' => 'required'
    //         ],
    //         'messages' => [
    //             'name.required' => 'Name is required',
    //             'email.required' => 'Email is required',
    //             'email.email' => 'Email is not valid',
    //             'phone.required' => 'Phone is required',
    //             'address.required' => 'Address is required'
    //         ],
            
        // ];

        $this->addRoute($data);
        $this->addNavbarMenu($data);
        $this->createMigration($data);
        $this->createModel($data);
        $this->createController($data);
        $this->createIndexView($data);
        $this->createFormView($data);
        $this->createPersmissions($data);
    }

    function addRoute($data){
        $useStatement = "use App\Http\Controllers\Admin\\".$data['controller_name'].";\n";
        $routeFile = file_get_contents(base_path('routes/backend.php'));
        if(strpos($routeFile, $useStatement) === false){
            $routeFile = str_replace("//End of use statements", $useStatement."//End of use statements", $routeFile);
            file_put_contents(base_path('routes/backend.php'), $routeFile);
        }

        $route = "\n";
        $route .= "        Route::resource('".$data['route_name']."', ".$data['controller_name']."::class);\n";
        $route .= "        Route::post('".$data['route_name']."/data', [".$data['controller_name']."::class, 'data'])->name('".$data['route_name'].".data');\n";
        $route .= "        Route::post('".$data['route_name']."/list',[".$data['controller_name']."::class, 'list'])->name('".$data['route_name'].".list');\n";
        $route .= "        Route::post('".$data['route_name']."/change-status',[".$data['controller_name']."::class, 'changeStatus'])->name('".$data['route_name'].".change.status');\n";
        $route .= "\n";

        //check if resource route already exists
        if(strpos($routeFile, "Route::resource('".$data['route_name']."', ".$data['controller_name']."::class)") === false){
            $routeFile = str_replace("//End of File", $route."        //End of File", $routeFile);
            file_put_contents(base_path('routes/backend.php'), $routeFile);
            $this->info('Route added successfully');
        }else{
            $this->info('Route already exists');
        }
            
    }

    function addNavbarMenu($data){
        $navbarFile = file_get_contents(resource_path('views/layouts/admin/navbar.blade.php'));
        $menu = "\n";
        $menu .= "        @can('".$data['route_name']."-view')\n";
        $menu .= "        <li class=\"sidebar-item\">\n";
        $menu .= "            <a class=\"sidebar-link @if(Route::is('admin.".$data['route_name'].".*')) active @endif\" href=\"{{ route('admin.".$data['route_name'].".index') }}\" aria-expanded=\"false\">\n";
        $menu .= "                <span>\n";
        $menu .= "                    <i class=\"ti ti-users\"></i>\n";
        $menu .= "                </span>\n";
        $menu .= "                <span class=\"hide-menu capitalize\">".$data['view_folder_name']."</span>\n";
        $menu .= "            </a>\n";
        $menu .= "        </li>\n";
        $menu .= "        @endcan\n";
        $menu .= "\n";
        
        //check if menu already exists
        if(strpos($navbarFile, "route('admin.".$data['route_name'].".index')") === false){
            $navbarFile = str_replace("<!-- End of File -->", $menu."        <!-- End of File -->", $navbarFile);
            file_put_contents(resource_path('views/layouts/admin/navbar.blade.php'), $navbarFile);
            $this->info('Navbar menu added successfully');
        }else{
            $this->info('Navbar menu already exists');
        }
    }

    function createMigration($data){
        $fields = "";
        foreach($data['fields'] as $field => $fieldType){
            if($fieldType == 'INPUT'){
                $fields .= "            \$table->string('".$field."')->nullable();\n";
            }else if($fieldType == 'TEXTAREA'){
                $fields .= "            \$table->text('".$field."')->nullable();\n";
            }else if($fieldType == 'SELECT'){
                if($field == 'status'){
                    $fields .= "            \$table->string('".$field."')->default('ACTIVE');\n";
                }else{
                    $fields .= "            \$table->string('".$field."');\n";
                }
            }
        }
        $fields .= "            \$table->unsignedBigInteger('created_by')->nullable();\n";
        $fields .= "            \$table->unsignedBigInteger('updated_by')->nullable();\n";
        
        $migration = "<?php \n\n";
        $migration .= "use Illuminate\Database\Migrations\Migration;\n";
        $migration .= "use Illuminate\Database\Schema\Blueprint;\n";
        $migration .= "use Illuminate\Support\Facades\Schema;\n\n";
        $migration .= "class Create".$data['table_name']."Table extends Migration\n";
        $migration .= "{\n";
        $migration .= "    public function up()\n";
        $migration .= "    {\n";
        $migration .= "        Schema::create('".$data['table_name']."', function (Blueprint \$table) {\n";
        $migration .= "            \$table->id();\n";
        $migration .= $fields;
        $migration .= "            \$table->timestamps();\n";
        $migration .= "\n";
        $migration .= "            \$table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');\n";
        $migration .= "            \$table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');\n";
        $migration .= "        });\n";
        $migration .= "    }\n";
        $migration .= "\n";
        $migration .= "    public function down()\n";
        $migration .= "    {\n";
        $migration .= "        Schema::dropIfExists('".$data['table_name']."');\n";
        $migration .= "    }\n";
        $migration .= "}\n";

        //check if migration already exists
        $file_ending_at = '_create_'.$data['table_name'].'_table.php';
        $migrationFiles = scandir(database_path('migrations'));
        $migrationExists = false;
        foreach($migrationFiles as $file){
            if(strpos($file, $file_ending_at) !== false){
                $migrationExists = true;
            }
        }
        if($migrationExists){
            $this->info('Migration already exists');
            return;
        }else{
            file_put_contents(database_path('migrations/'.date('Y_m_d_His').'_create_'.$data['table_name'].'_table.php'), $migration);
            $this->info('Migration created successfully');
        }
    }
    
    function createModel($data){
        $model = "<?php \n\n"; 
        $model .= "namespace App\Models;\n\n";
        $model .= "use Illuminate\Database\Eloquent\Factories\HasFactory;\n";
        $model .= "use Illuminate\Database\Eloquent\Model;\n\n";
        $model .= "use App\Models\BaseModel;\n\n";

        $model .= "class ".$data['model_name']." extends BaseModel\n";
        $model .= "{\n";
        $model .= "    protected \$fillable = [\n";
            foreach($data['fields'] as $field => $fieldType){
                if($field != ''){
                    $model .= "        '".$field."',\n";
                }
            }
        $model .= "    ];\n";

        $model .= "\n";
        $model .= "}\n";
        
        file_put_contents(app_path('Models/'.$data['model_name'].'.php'), $model);
        $this->info('Model created successfully');
    }

    function createController($data){
        $controller = "<?php \n\n";
        $controller .= "namespace App\Http\Controllers\Admin;\n\n";
        $controller .= "use DataTables;\n";
        $controller .= "use App\Http\Controllers\Controller;\n";
        $controller .= "use Illuminate\Http\Request;\n";
        $controller .= "use App\Models\\".$data['model_name'].";\n\n";

        //Controller Start ------------------------------------------------------------
        $controller .= "class ".$data['controller_name']." extends Controller\n";
        $controller .= "{\n";

        //Index Function ------------------------------------------------------------
        $controller .= "    public function index(){\n";
        $controller .= "        return view('Admin.".$data['view_folder_name'].".index');\n";
        $controller .= "    }\n";
        $controller .= "\n";

        //Data Function ------------------------------------------------------------
        $controller .= "    public function data(){\n";
        $controller .= "        \$query = ".$data['model_name']."::where('id', '!=', 0)->orderByDesc('id');\n";
        $controller .= "\n";
        $controller .= "        return DataTables::eloquent(\$query)\n";

                                //Fields ------------------------------
                                foreach($data['fields'] as $field => $fieldType)
                                {
                                    if($field != 'status' && $field != ''){
        $controller .= "            ->editColumn('".$field."', function (\$".$data['singular_name'].") {\n";
        $controller .= "                return \$".$data['singular_name']."->".$field.";\n";
        $controller .= "            })\n";
                                    }
                                }
                                
                                //Status Switch Button ------------------------------
                                foreach($data['fields'] as $field => $fieldType){
                                    if($field == 'status' && $field != ''){
        $controller .= "            ->editColumn('status', function (\$".$data['singular_name'].") {\n";
        $controller .= "                if (\$".$data['singular_name']."->status == 'ACTIVE') {\n";
        $controller .= "                    return '<div class=\"form-check form-switch\"><input class=\"form-check-input slider-status-switch\" type=\"checkbox\" checked data-routekey=\"' . \$".$data['singular_name']."->route_key . '\"/></div>';\n";
        $controller .= "                } else {\n";
        $controller .= "                    return '<div class=\"form-check form-switch\"><input class=\"form-check-input slider-status-switch\" type=\"checkbox\" data-routekey=\"' . \$".$data['singular_name']."->route_key . '\"/></div>';\n";
        $controller .= "                }\n";
        $controller .= "            })\n";
                                    }
                                }

                                //Action Column ------------------------------
        $controller .= "            ->addColumn('action', function (\$".$data['singular_name'].") {\n";
        $controller .= "                \$edit  = '<a href=\"'.route('admin.".$data['route_name'].".edit',['".$data['singular_name']."' => \$".$data['singular_name']."->route_key]).'\" class=\"badge bg-warning fs-1\"><i class=\"fa fa-edit\"></i></a>';\n";
        $controller .= "                return \$edit;\n";
        $controller .= "            })\n";

        $controller .= "            ->addIndexColumn()\n";
                                    $fields = "";
                                    foreach($data['fields'] as $field => $fieldType){
                                        if($field != ''){
                                            $fields .= "'".$field."',";
                                        }
                                    }
        $controller .= "            ->rawColumns([".$fields."'action'])\n";
        $controller .= "            ->setRowId('id')\n";
        $controller .= "            ->make(true);\n";
        $controller .= "    }\n";
        $controller .= "\n";

        //Create Function ------------------------------------------------------------
        $controller .= "    public function create(){\n";
        $controller .= "        return view('Admin.".$data['view_folder_name'].".form');\n";
        $controller .= "    }\n";
        $controller .= "\n";

        //Store Function ------------------------------------------------------------
        $controller .= "    public function store(Request \$request)\n";
        $controller .= "    {\n";
        $controller .= "        \$request->validate(\$this->rules, \$this->customMessages); \n";
        $controller .= "\n";
        $controller .= "        \$".$data['singular_name']." = new ".$data['model_name'].";\n";
        $controller .= "        \$".$data['singular_name']."->fill(\$request->all());\n";
        $controller .= "        \$".$data['singular_name']."->save();\n";
        $controller .= "\n";
        $controller .= "        return response()->json([\n";
        $controller .= "            'status' => 'success',\n";
        $controller .= "            'message' => '".$data['model_name']." Created Successfully',\n";
        $controller .= "            'slider' =>  \$".$data['singular_name']."\n";
        $controller .= "        ], 201);\n";
        $controller .= "    }\n";
        $controller .= "\n";

        //Edit Function --------------------------------------------------------------------------------
        $controller .= "    public function edit(".$data['model_name']." \$".$data['singular_name']."){\n";
        $controller .= "        return view('Admin.".$data['view_folder_name'].".form', compact('".$data['singular_name']."'));\n";
        $controller .= "    }\n";
        $controller .= "\n";
        
        //Update Function ------------------------------------------------------------------------------------------
        $controller .= "    public function update(Request \$request, ".$data['model_name']." \$".$data['singular_name'].")\n";
        $controller .= "    {\n";
        $controller .= "        \$request->validate(\$this->rules, \$this->customMessages); \n";
        $controller .= "        \$".$data['singular_name']."->fill(\$request->all());\n";
        $controller .= "        \$".$data['singular_name']."->save();\n";
        $controller .= "\n";
        $controller .= "        return response()->json([\n";
        $controller .= "            'status' => 'success',\n";
        $controller .= "            'message' => '".$data['model_name']." Created Successfully',\n";
        $controller .= "            'slider' =>  \$".$data['singular_name']."\n";
        $controller .= "        ], 201);\n";
        $controller .= "    }\n";
        $controller .= "\n";

        //Destroy Function ------------------------------------------------------------------------------------------
        $controller .= "    public function destroy(\$id){\n";
        $controller .= "        \n";
        $controller .= "    }\n";
        $controller .= "\n";

        //Validation Rules --------------------------------------------------------------------------------
        $controller .= "    private \$rules = [\n";
            foreach($data['validations'] as $key => $value){
                $controller .= "        '".$key."' => '".$value."',\n";
            }
        $controller .= "    ];\n";

        //Validation Messages ------------------------------------------------------------------------------------------
        $controller .= "    private \$customMessages = [\n";
            foreach($data['messages'] as $key => $value){
                $controller .= "        '".$key."' => '".$value."',\n";
            }
        $controller .= "    ];\n";

        //Controller End
        $controller .= "}\n";

        file_put_contents(app_path('Http/Controllers/Admin/'.$data['controller_name'].'.php'), $controller);
        $this->info('Controller created successfully');
    }

    function createIndexView($data){
        $index = "@extends('layouts.admin')\n";
        $index .= "@section('title') ".$data['model_name']." @endsection\n";
        $index .= "@section('content')\n";
        $index .= "<section>\n";
        $index .= "    <div class=\"row\">\n";
        $index .= "        <div class=\"col-12\">\n";
        $index .= "            <div class=\"card w-100 position-relative overflow-hidden\">\n";
        $index .= "                <div class=\"card-header px-4 py-3 border-bottom\">\n";
        $index .= "                    <div class=\"row\">\n";
        $index .= "                        <div class=\"col-6 d-flex justify-content-start\">\n";
        $index .= "                            <h5 class=\"card-title fw-semibold mb-0 lh-sm\">".$data['model_name']."</h5>\n";
        $index .= "                        </div>\n";
        $index .= "                        <div class=\"col-6 d-flex justify-content-end\">\n";
        $index .= "                            <a href=\"{{ route('admin.".$data['route_name'].".create') }}\" class=\"btn btn-info\">\n";
        $index .= "                                Create\n";
        $index .= "                                &nbsp;\n";
        $index .= "                                <i class=\"ti ti-plus\"></i>\n";
        $index .= "                            </a>\n";
        $index .= "                        </div>\n";
        $index .= "                    </div>\n";
        $index .= "                </div>\n";
        $index .= "                <div class=\"card-body p-4\">\n";
        $index .= "                    <div class=\"table-responsive rounded-2 mb-4\">\n";
        $index .= "                    <table class=\"table border table-bordered table-sm text-nowrap mb-0 align-middle\" id=\"datatable\">\n";
        $index .= "                        <thead class=\"text-dark fs-3\">\n";
        $index .= "                            <tr>\n";
        $index .= "                                <th width=\"3%\">\n";
        $index .= "                                    <h6 class=\"fs-3 fw-semibold mb-0\">#</h6>\n";
        $index .= "                                </th>\n";
        $index .= "                                <th width=\"3%\">\n";
        $index .= "                                    <h6 class=\"fs-3 fw-semibold mb-0\">Action</h6>\n";
        $index .= "                                </th>\n";
                                            foreach($data['fields'] as $field => $fieldType){
                                                if($field == 'status' && $field != ''){
        $index .= "                                <th width=\"3%\">\n";
        $index .= "                                    <h6 class=\"fs-3 fw-semibold mb-0\">Status</h6>\n";
        $index .= "                                </th>\n";
                                                }
                                            }
                                            foreach($data['fields'] as $field => $fieldType){
                                                if($field != 'status' &&  $field != ''){
        $index .= "                                <th width=\"10%\">\n";
        $index .= "                                    <h6 class=\"fs-3 fw-semibold mb-0\">".ucwords(str_replace('_', ' ', $field))."</h6>\n";
        $index .= "                                </th>\n";
                                                }
                                            }
       
        $index .= "                            </tr>\n";
        $index .= "                        </thead>\n";
        $index .= "                    </table>\n";
        $index .= "                </div>\n";
        $index .= "            </div>\n";
        $index .= "        </div>\n";
        $index .= "    </div>\n";
        $index .= "</section>\n";
        $index .= "<script type=\"text/javascript\">\n";
        $index .= "    $(function() {\n";
        $index .= "        var dataTable = $('#datatable').DataTable({\n";
        $index .= "            dom: \"Bfrtip\",\n";
        $index .= "            buttons: [\"copy\", \"csv\", \"excel\", \"pdf\", \"print\"],\n";
        $index .= "            processing: true,\n";
        $index .= "            serverSide: true,\n";
        $index .= "            scrollCollapse: true,\n";
        $index .= "            scrollX:false,\n";
        $index .= "            ajax: {\n";
        $index .= "                url: '{!! route('admin.".$data['route_name'].".data') !!}',\n";
        $index .= "                type: 'POST',\n";
        $index .= "                data: function(d) {\n";
        $index .= "                    d._token = $('meta[name=csrf-token]').attr('content');\n";
        $index .= "                }\n";
        $index .= "            },\n";
        $index .= "            columns: [\n";
        $index .= "                {data: 'DT_RowIndex', orderable: false, searchable: false},\n";
        $index .= "                {data: 'action',name: '".$data['table_name'].".id',searchable: false},\n";
                            foreach($data['fields'] as $field => $fieldType){
                                if($field == 'status' && $field != ''){
        $index .= "                {data: 'status',name: '".$data['table_name'].".id',searchable: false},\n";
                                }
                            }
                            foreach($data['fields'] as $field => $fieldType){
                                if($field != 'status' && $field != ''){
        $index .= "                {data: '".$field."',name: '".$data['table_name'].".".$field."'},\n";
                                }
                            }
        $index .= "            ],\n";
        $index .= "            order: [],\n";
        $index .= "            columnDefs: [\n";
        $index .= "                { targets: [0,1], className: \"text-center\"},\n";
        $index .= "            ],\n";
        $index .= "        });\n";
        $index .= "        $(\".buttons-copy, .buttons-csv, .buttons-print, .buttons-pdf, .buttons-excel\").addClass(\"btn btn-primary mr-1\");\n";
        $index .= "    });\n";
        $index .= "</script>\n";
        $index .= "@endsection\n";
        
        //create directory if not exists
        if(!is_dir(resource_path('views/Admin/'.$data['view_folder_name']))){
            mkdir(resource_path('views/Admin/'.$data['view_folder_name']));
        }
        file_put_contents(resource_path('views/Admin/'.$data['view_folder_name'].'/index.blade.php'), $index);
    }

    function createFormView($data){
        $form = "@extends('layouts.admin')\n";
        $form .= "@section('title')\n";
        $form .= "    ".$data['model_name']."\n";
        $form .= "@endsection\n";
        $form .= "@section('content')\n";
        $form .= "    <form method=\"POST\"\n";
        $form .= "        action=\"{{ Route::is('admin.".$data['route_name'].".create') ? route('admin.".$data['route_name'].".store') : route('admin.".$data['route_name'].".update', ['".$data['singular_name']."' => \$".$data['singular_name']."->route_key]) }}\"\n";
        $form .= "        method=\"POST\" enctype=\"multipart/form-data\" autocomplete=\"off\" id=\"".$data['table_name']."-form\">\n";
        $form .= "        @csrf\n";
        $form .= "        {{ Route::is('admin.".$data['route_name'].".create') ? '' : method_field('PUT') }}\n";
        $form .= "        <div class=\"row\">\n";
        $form .= "            <div class=\"col-lg-12 d-flex align-items-stretch\">\n";
        $form .= "                <div class=\"card w-100\">\n";
        $form .= "                    <div class=\"card-header\">\n";
        $form .= "                        <h5> {{ Route::is('admin.".$data['route_name'].".create') ? 'Create' : 'Edit' }} ".$data['model_name']." </h5>\n";
        $form .= "                    </div>\n";
        $form .= "                    <div class=\"card-body border-top\">\n";
        $form .= "                        <div class=\"row\">\n";
                                            foreach($data['fields'] as $field => $fieldType){
                                                if($fieldType == 'INPUT'){
        $form .= "                            <div class=\"col-sm-12 col-md-6\">\n";
        $form .= "                                <label class=\"control-label col-form-label\">".ucwords(str_replace('_', ' ', $field))." <sup class=\"tcul-star-restrict\">*</sup></label>\n";
        $form .= "                                <input type=\"text\" class=\"form-control\" placeholder=\"".ucwords(str_replace('_', ' ', $field))."\" name=\"".$field."\"\n";
        $form .= "                                    value=\"{{ isset(\$".$data['singular_name'].") ? \$".$data['singular_name']."->".$field." : '' }}\" />\n";
        $form .= "                                <div id=\"".$field."-error\" style=\"color:red\"></div>\n";
        $form .= "                            </div>\n";
                                                }else if($fieldType == 'TEXTAREA'){
        $form .= "                            <div class=\"col-sm-12 col-md-6\">\n";
        $form .= "                                <label class=\"control-label col-form-label\">".ucwords(str_replace('_', ' ', $field))." <sup class=\"tcul-star-restrict\">*</sup></label>\n";
        $form .= "                                <textarea class=\"form-control\" placeholder=\"".ucwords(str_replace('_', ' ', $field))."\" name=\"".$field."\">{{ isset(\$".$data['singular_name'].") ? \$".$data['singular_name']."->".$field." : '' }}</textarea>\n";
        $form .= "                                <div id=\"".$field."-error\" style=\"color:red\"></div>\n";
        $form .= "                            </div>\n";
                                                }else if($fieldType == 'SELECT'){
        $form .= "                            <div class=\"col-sm-12 col-md-6\">\n";
        $form .= "                                <label class=\"control-label col-form-label\">".ucwords(str_replace('_', ' ', $field))." <sup class=\"tcul-star-restrict\">*</sup></label>\n";
        $form .= "                                <select class=\"form-control\" name=\"".$field."\">\n";
        $form .= "                                    <option value=\"\">Select ".ucwords(str_replace('_', ' ', $field))."</option>\n";
        $form .= "                                    <option value=\"ACTIVE\" {{ isset(\$".$data['singular_name'].") && \$".$data['singular_name']."->".$field." == 'ACTIVE' ? 'selected' : '' }}>Active</option>\n";
        $form .= "                                    <option value=\"INACTIVE\" {{ isset(\$".$data['singular_name'].") && \$".$data['singular_name']."->".$field." == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>\n";
        $form .= "                                </select>\n";
        $form .= "                                <div id=\"".$field."-error\" style=\"color:red\"></div>\n";
        $form .= "                            </div>\n";
                                                }
                                            }
        $form .= "                        </div>\n";
        $form .= "                    </div>\n";
        $form .= "                    <div class=\"card-footer\">\n";
        $form .= "                        <button type=\"submit\" class=\"btn btn-primary\">\n";
        $form .= "                            Save\n";
        $form .= "                            &nbsp;\n";
        $form .= "                            <i class=\"ti ti-device-floppy\"></i>\n";
        $form .= "                        </button>\n";
        $form .= "                        &nbsp;&nbsp;&nbsp;&nbsp;\n";
        $form .= "                        <a href=\"{{ route('admin.".$data['route_name'].".index') }}\" type=\"button\" class=\"btn btn-secondary\">\n";
        $form .= "                            Cancel\n";
        $form .= "                            &nbsp;\n";
        $form .= "                            <i class=\"ti ti-arrow-back-up-double\"></i>\n";
        $form .= "                        </a>\n";
        $form .= "                    </div>\n";
        $form .= "                </div>\n";
        $form .= "            </div>\n";
        $form .= "        </div>\n";
        $form .= "    </form>\n";
        $form .= "<script>\n";
        $form .= "    $('#".$data['table_name']."-form').submit(function(e) {\n";
        $form .= "        e.preventDefault();\n";
        $form .= "        $('div[id$=\"-error\"]').empty();\n";
        $form .= "        var form = $(this);\n";
        $form .= "        var url = form.attr('action');\n";
        $form .= "        $.ajax({\n";
        $form .= "            type: \"POST\",\n";
        $form .= "            url: url,\n";
        $form .= "            data: new FormData(this),\n";
        $form .= "            contentType: false,\n";
        $form .= "            cache: false,\n";
        $form .= "            processData: false,\n";
        $form .= "            success: function(data) {\n";
        $form .= "                if (data.status == 'success') {\n";
        $form .= "                    toastr.success(data.message, '', {\n";
        $form .= "                        showMethod: \"slideDown\",\n";
        $form .= "                        hideMethod: \"slideUp\",\n";
        $form .= "                        timeOut: 1500,\n";
        $form .= "                        closeButton: true,\n";
        $form .= "                    });\n";
        $form .= "                    setTimeout(function() {\n";
        $form .= "                        window.location.href = \"{!! route('admin.".$data['route_name'].".index') !!}\";\n";
        $form .= "                    }, 100);\n";
        $form .= "                } else {\n";
        $form .= "                    toastr.error('There is some error!!', '', {\n";
        $form .= "                        showMethod: \"slideDown\",\n";
        $form .= "                        hideMethod: \"slideUp\",\n";
        $form .= "                        timeOut: 1500,\n";
        $form .= "                        closeButton: true,\n";
        $form .= "                    });\n";
        $form .= "                }\n";
        $form .= "            },\n";
        $form .= "            error: function(xhr, ajaxOptions, thrownError) {\n";
        $form .= "                toastr.error('There are some errors in Form. Please check your inputs', '', {\n";
        $form .= "                    showMethod: \"slideDown\",\n";
        $form .= "                    hideMethod: \"slideUp\",\n";
        $form .= "                    timeOut: 1500,\n";
        $form .= "                    closeButton: true,\n";
        $form .= "                });\n";
        $form .= "                $.each(xhr.responseJSON.errors, function(key, value) {\n";
        $form .= "                    $('#' + key + '-error').html(value);\n";
        $form .= "                });\n";
        $form .= "                $('html, body').animate({\n";
        $form .= "                    scrollTop: $('#' + Object.keys(xhr.responseJSON.errors)[0] + '-error')\n";
        $form .= "                        .offset().top - 200\n";
        $form .= "                }, 500);\n";
        $form .= "            }\n";
        $form .= "        });\n";
        $form .= "    });\n";
        $form .= "</script>\n";
        $form .= "@endsection\n";

        //create directory if not exists
        if(!is_dir(resource_path('views/Admin/'.$data['view_folder_name']))){
            mkdir(resource_path('views/Admin/'.$data['view_folder_name']));
        }
        file_put_contents(resource_path('views/Admin/'.$data['view_folder_name'].'/form.blade.php'), $form);
    }

    public function createPersmissions($data){
        $permission = '';
        $permission .= "'".$data['model_name']."' => [\n";
        $permission .= "            'controller' => 'Admin\\".$data['controller_name']."',\n";
        $permission .= "            'permissions' => [\n";
        $permission .= "                '".$data['route_name']."-view' => [\n";
        $permission .= "                    'index',\n";
        $permission .= "                    'data',\n";
        $permission .= "                ],\n";
        $permission .= "                '".$data['route_name']."-store' => [\n";
        $permission .= "                    'create',\n";   
        $permission .= "                    'store',\n";
        $permission .= "                ],\n";
        $permission .= "                '".$data['route_name']."-update' => [\n";
        $permission .= "                    'edit',\n";
        $permission .= "                    'update',\n";
        $permission .= "                ],\n";
        $permission .= "            ]\n";
        $permission .= "        ],\n";

        $permissionSeederFile = file_get_contents(database_path('seeders/PermissionSeeder.php'));
        if(strpos($permissionSeederFile, "'".$data['model_name']."'") === false){
            $permissionSeederFile = str_replace("//End of Permission Arr", $permission."        //End of Permission Arr", $permissionSeederFile);
            file_put_contents(database_path('seeders/PermissionSeeder.php'), $permissionSeederFile);
        }

        $role_permission = "";
        $role_permission .= "   #".$data['model_name']."\n";
        $role_permission .= "            '".$data['route_name']."-view',\n";
        $role_permission .= "            '".$data['route_name']."-store',\n";
        $role_permission .= "            '".$data['route_name']."-update',\n";

        $rolePermissionSeederFile = file_get_contents(database_path('seeders/PermissionSeeder.php'));
        if(strpos($rolePermissionSeederFile, "#".$data['route_name']."-view,") === false){
            $rolePermissionSeederFile = str_replace("//End of Role Permission", $role_permission."\n            //End of Role Permission", $rolePermissionSeederFile);
            file_put_contents(database_path('seeders/PermissionSeeder.php'), $rolePermissionSeederFile);
        }

        $this->info('Permissions added successfully');
    }
}
