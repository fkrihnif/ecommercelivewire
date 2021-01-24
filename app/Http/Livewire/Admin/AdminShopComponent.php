<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Product;
use Livewire\WithFileUploads;

class AdminShopComponent extends Component
{
    use WithFileUploads;
    public $students, $name, $nim, $image, $student_id;
    public $updateMode = false;

    public function render()
    {
        $this->students = Product::orderBy('id', 'DESC')->get();
        return view('livewire.student.students');
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->nim = '';
        $this->image = '';
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'nim' => 'required|min:5',
            'image' => 'required|image'
        ]);

        Product::create([
            'name' => $this->name,
            'nim' => $this->nim,
            'image' => $this->image->store('assets/students', 'public')
        ]);

        session()->flash('message', 'Product Created Successfully.');

        $this->resetInputFields();

        $this->emit('userStore'); // Close model to using to jquery

    }

    public function edit($id)
    {
        $this->updateMode = true;
        $student = Product::where('id', $id)->first();

        $this->student_id = $id;
        $this->name = $student->name;
        $this->nim = $student->nim;
        $this->image = $student->image;
    }

    public function cancel()
    {
        $this->updateMode = false;
        $this->resetInputFields();
    }

    public function update()
    {
        $validatedDate = $this->validate([
            'name' => 'required',
            'nim' => 'required|min:5',
            'image' => 'required|image'
        ]);

        if ($this->student_id) {
            $user = Product::find($this->student_id);
            $user->update([
                'name' => $this->name,
                'nim' => $this->nim,
                'image' => $this->image->store('assets/students', 'public')
            ]);
            $this->updateMode = false;
            session()->flash('message', 'Product Updated Successfully.');
            $this->resetInputFields();
        }
    }

    public function delete($id)
    {
        if ($id) {
            Product::where('id', $id)->delete();
            session()->flash('message', 'Product Deleted Successfully.');
        }
    }
}
