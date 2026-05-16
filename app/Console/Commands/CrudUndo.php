<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CrudUndo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crud:undo {id}';

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
        
        
        $this->deleteRoute($crud);
        $this->deleteNavbarMenu($crud);
        $this->deleteMigration($crud);
        $this->deleteModel($crud);
        $this->deleteController($crud);
        $this->deleteIndexView($crud);
        $this->deleteFormView($crud);
        $this->deletePersmissions($crud);
    }

    public function deleteRoute($crud)
    {
        $routeFile = base_path('routes/backend.php');
        $routeContent = file_get_contents($routeFile);
        $routeContent = str_replace("Route::resource('".$crud->route_name."', ".$crud->controller_name."::class);\n", '', $routeContent);
        $routeContent = str_replace("Route::post('".$crud->route_name."/data', [".$crud->controller_name."::class, 'data'])->name('".$crud->route_name.".data');\n", '', $routeContent);
        $routeContent = str_replace("Route::post('".$crud->route_name."/list',[".$crud->controller_name."::class, 'list'])->name('".$crud->route_name.".list');\n", '', $routeContent);
        $routeContent = str_replace("Route::post('".$crud->route_name."/change-status',[".$crud->controller_name."::class, 'changeStatus'])->name('".$crud->route_name.".change.status');\n", '', $routeContent);
        $routeContent = str_replace("                //End of File","        //End of File", $routeContent);
        file_put_contents($routeFile, $routeContent);
    }

    public function deleteNavbarMenu($crud)
    {
        $menu = "\n";
        $menu .= "        @can('".$crud->route_name."-view')\n";
        $menu .= "        <li class=\"sidebar-item\">\n";
        $menu .= "            <a class=\"sidebar-link @if(Route::is('admin.".$crud->route_name.".*')) active @endif\" href=\"{{ route('admin.".$crud->route_name.".index') }}\" aria-expanded=\"false\">\n";
        $menu .= "                <span>\n";
        $menu .= "                    <i class=\"ti ti-users\"></i>\n";
        $menu .= "                </span>\n";
        $menu .= "                <span class=\"hide-menu capitalize\">".$crud->view_folder_name."</span>\n";
        $menu .= "            </a>\n";
        $menu .= "        </li>\n";
        $menu .= "        @endcan\n";
        $menu .= "\n";

        $menuFile = base_path('resources/views/layouts/admin/navbar.blade.php');
        $menuContent = file_get_contents($menuFile);
        $menuContent = str_replace($menu,"", $menuContent);
        $menuContent = str_replace("                <!-- End of File -->","        <!-- End of File -->", $menuContent);
        file_put_contents($menuFile, $menuContent);
    }

    public function deleteMigration($crud)
    {
        $migrationFile = base_path('database/migrations');
        $migrationFiles = scandir($migrationFile);
        foreach($migrationFiles as $file){
            if(strpos($file, $crud->table_name) !== false){
                unlink($migrationFile.'/'.$file);
            }
        }
    }

    public function deleteModel($crud)
    {
        $modelFile = app_path('Models/'.$crud->model_name.'.php');
        unlink($modelFile);
    }

    public function deleteController($crud)
    {
        $controllerFile = app_path('Http/Controllers/Admin/'.$crud->controller_name.'.php');
        unlink($controllerFile);
    }

    public function deleteIndexView($crud)
    {
        $indexViewFile = base_path('resources/views/Admin/'.$crud->view_folder_name.'/index.blade.php');
        unlink($indexViewFile);
    }

    public function deleteFormView($crud)
    {
        $formViewFile = base_path('resources/views/Admin/'.$crud->view_folder_name.'/form.blade.php');
        unlink($formViewFile);
    }

    public function deletePersmissions($crud)
    {
        $permission = '';
        $permission .= "'".$crud->model_name."' => [\n";
        $permission .= "            'controller' => 'Admin\\".$crud->controller_name."',\n";
        $permission .= "            'permissions' => [\n";
        $permission .= "                '".$crud->route_name."-view' => [\n";
        $permission .= "                    'index',\n";
        $permission .= "                    'data',\n";
        $permission .= "                ],\n";
        $permission .= "                '".$crud->route_name."-store' => [\n";
        $permission .= "                    'create',\n";   
        $permission .= "                    'store',\n";
        $permission .= "                ],\n";
        $permission .= "                '".$crud->route_name."-update' => [\n";
        $permission .= "                    'edit',\n";
        $permission .= "                    'update',\n";
        $permission .= "                ],\n";
        $permission .= "            ]\n";
        $permission .= "        ],\n";

        $permissionSeederFile = file_get_contents(database_path('seeders/PermissionSeeder.php'));
        $permissionSeederFile = str_replace($permission,"", $permissionSeederFile);
        $permissionSeederFile = str_replace("                //End of Permission Arr","        //End of Permission Arr", $permissionSeederFile);
        file_put_contents(database_path('seeders/PermissionSeeder.php'), $permissionSeederFile);

        $permissionFile = base_path('database/seeders/PermissionSeeder.php');
        $permissionContent = file_get_contents($permissionFile);
        $permissionContent = str_replace("#".$crud->model_name."\n", '', $permissionContent);
        $permissionContent = str_replace("'".$crud->route_name."-view',\n", '', $permissionContent);
        $permissionContent = str_replace("'".$crud->route_name."-store',\n", '', $permissionContent);
        $permissionContent = str_replace("'".$crud->route_name."-update',", '', $permissionContent);
        $permissionContent = str_replace("            //End of Role Permission","            //End of Role Permission", $permissionContent);
        file_put_contents($permissionFile, $permissionContent);
    }
    
}
