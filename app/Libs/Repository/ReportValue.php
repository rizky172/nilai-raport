<?php

namespace App\Libs\Repository;

use DB;

use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Libs\Repository\AbstractRepository;

use App\Models\ReportValue as Model;
use App\Models\ReportValueDetail;
use App\Models\Person;
use App\Models\Category;

class ReportValue extends AbstractRepository
{
     protected $details = [];

    public function __construct(Model $model)
    {
        parent::__construct($model);
    }

    public function addDetail($id, $studentId, $value)
    {
        $this->details[] = [
            'id' => $id,
            'student_id' => $studentId,
            'value' => $value
        ];
    }

    private function generateData()
    {
        if (empty($this->model->ref_no)) {
            $this->model->ref_no = $this->generateRefNo(4, $this->getPrefix(), $this->getPostfix());
        }
    }

    public function getPrefix()
    {
        return 'NR/';
    }

    public function getPostfix()
    {
        return '/' . date('m.y');
    }

    private function validateBasic()
    {
        $id = $this->model->id;
        $teacherId = $this->model->teacher_id;
        $classId = $this->model->class_id;
        $majorId = $this->model->major_id;
        $lessonId = $this->model->lesson_id;
        $semesterId = $this->model->semester_id;

        $fields = [
            'teacher_id' => $this->model->teacher_id,
            'class_id' => $this->model->class_id,
            'major_id' => $this->model->major_id,
            'lesson_id' => $this->model->lesson_id,
            'semester_id' => $this->model->semester_id,
            'ref_no' => $this->model->ref_no
        ];

        $rules = [
            'teacher_id' => [
                'required',
                'exists:person,id',
                Rule::unique('report_value')->where(function ($query) use($teacherId, $classId, $majorId, $lessonId, $semesterId, $id) {
                    $query = $query->where('teacher_id', $teacherId)
                                    ->where('class_id', $classId)
                                    ->where('major_id', $majorId)
                                    ->where('lesson_id', $lessonId)
                                    ->where('semester_id', $semesterId);

                    if (!empty($id)) {
                        $query = $query->where('id', '!=', $id);
                    }

                    return $query;
                }),
            ],
            'class_id' => 'required|exists:category,id',
            'major_id' => 'required|exists:category,id',
            'lesson_id' => 'required|exists:category,id',
            'semester_id' => 'required|exists:category,id',
            'ref_no' => 'required|unique:report_value,ref_no,'.$this->model->id
        ];

        $messages = [
            'teacher_id.unique' => 'Data Raport sudah memiliki Guru, Kelas, Jurusan, Mata Pelajaran, Semester yg sama.',
            'teacher_id.required' => 'Guru wajib diisi.',
            'teacher_id.exists' => 'Guru tidak valid.',
            'class_id.required' => 'Kelas wajib diisi.',
            'class_id.exists' => 'Kelas tidak valid.',
            'major_id.required' => 'Jurusan wajib diisi.',
            'major_id.exists' => 'Jurusan tidak valid.',
            'lesson_id.required' => 'Mata Pelajaran wajib diisi.',
            'lesson_id.exists' => 'Mata Pelajaran tidak valid.',
            'semester_id.required' => 'Semester wajib diisi.',
            'semester_id.exists' => 'Semester tidak valid.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    private function validateDetails()
    {
        $fields = [
            'details' => $this->details
        ];

        $rules = [
            'details.required',
            'details.*.student_id' => 'required|exists:person,id',
            'details.*.value' => 'required|numeric|min:0|max:100',
        ];

        $messages = [
            'details.required' => 'Detail Nilai Raport wajib diisi.'
        ];

        $persons = Person::all();

        foreach($this->details as $key => $value){
            $person = $persons->firstWhere('id', $value['student_id']);

            $messages['details.' . $key . '.value.max'] = sprintf('Siswa %s Nilai Raport tidak boleh lebih dari 100', $person->name);
        }

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public function validate()
    {
        parent::validate();

        $this->validateBasic();
        $this->validateDetails();
    }

    public function beforeSave()
    {
        $this->generateData();

        parent::beforeSave();
    }

    public function save()
    {
        $this->filterByAccessControl('report_value_create');

        parent::save();
    }

    public function afterSave()
    {
        $this->saveDetail();
    }

    protected function saveDetail()
    {
        foreach ($this->details as $key => $value) {
            $detail = ReportValueDetail::findOrNew($value['id']);
            $detail->report_value_id = $this->model->id;
            $detail->student_id = $value['student_id'];
            $detail->value = $value['value'];
            $detail->save();
        }
    }

    public function delete($permanent = null)
    {
        $this->filterByAccessControl('report_value_delete');

        ReportValueDetail::where('report_value_id', $this->model->id)->delete();

        parent::delete($permanent);
    }

    public function toArray()
    {
        $this->filterByAccessControl('report_value_read');

        $data = $this->model->toArray();

        $detail = [];
        foreach ($this->model->details as $key => $value) {
            $detail[] = [
                'id' => $value['id'],
                'student_id' => $value['student_id'],
                'name' => $value->student->name,
                'value' => $value['value']
            ];
        }

        $data['detail'] = $detail;

        return $data;
    }

    private function validateReportValue($teacherId, $classId, $majorId)
    {
        $fields = [
            'teacher_id' => $teacherId,
            'class_id' => $classId,
            'major_id' => $majorId
        ];

        $rules = [
            'teacher_id' => 'required',
            'class_id' => 'required',
            'major_id' => 'required'
        ];

        $messages = [
            'teacher_id.required' => 'Guru wajib diisi.',
            'class_id.required' => 'Kelas wajib diisi.',
            'major_id.required' => 'Jurusan tidak valid.'
        ];

        $validator = Validator::make($fields, $rules, $messages);
        self::validOrThrow($validator);
    }

    public function getReportValue($teacherId, $classId, $majorId)
    {
        $this->validateReportValue($teacherId, $classId, $majorId);
        
        $category = Category::where('group_by', 'person')
                            ->where('name', 'student')
                            ->first();

        $person = Person::select('person.id', 'person.name')
                        ->where('person_category_id', $category->id)
                        ->where('major_id', $majorId)
                        ->join('meta', function ($join) use ($classId) {
                            $join->on('meta.table_name', '=', DB::raw("'person'"));
                            $join->on('meta.key', '=', DB::raw("'class_id'"));
                            $join->on('meta.fk_id', '=', "person.id");
                            $join->on('meta.value', '=', DB::raw($classId));
                        })
                        ->get();

        $data = [];
        foreach($person as $x){
            $data[] = [
                'id' => $x->id,
                'name' => $x->name
            ];
        }

        return $data;
    }
}
