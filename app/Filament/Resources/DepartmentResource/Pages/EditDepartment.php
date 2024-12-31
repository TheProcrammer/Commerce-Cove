<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepartment extends EditRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    // Getting the index page from DepartmentResource. It is already declared and you can call it anytime
    // if you need it. Other pages like index, create, edit.
    // It directs you from the index page once you update a department.
    protected function getRedirectUrl():string
    {
        return $this->getResource()::getUrl('index');
    }
}
