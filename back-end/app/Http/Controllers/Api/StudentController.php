<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\User;
use App\Support\SchoolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StudentController extends Controller
{
    public function index()
    {
        if (! SchoolAccess::canManageSchool(request()->user())) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return response()->json(
            SchoolAccess::scopeStudents($this->studentsQuery(), request()->user())->get(),
            200,
            [],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function show($studentId)
    {
        $user = request()->user();
        if (! SchoolAccess::canManageSchool($user) && (string) $user?->student_id !== (string) $studentId) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $query = $this->studentsQuery();
        if (SchoolAccess::canManageSchool($user)) {
            $query = SchoolAccess::scopeStudents($query, $user);
        }

        $student = $query
            ->where('students.student_id', $studentId)
            ->first();

        if (! $student) {
            return response()->json(['message' => 'Student not found'], 404);
        }

        return response()->json(['student' => $student], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function store(Request $request)
    {
        if (! SchoolAccess::canManageSchool($request->user())) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'student_id' => 'required|integer|unique:students,student_id|unique:users,student_id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'password' => 'nullable|string|min:6',
            'class' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'grade_level' => 'nullable|string|max:100',
            'stage' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'status' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'parent_contact' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'gpa' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($validated) {
            $scope = SchoolAccess::supervisorScope(request()->user());
            if ($scope) {
                $validated['stage'] = $scope['stage'];
                $validated['gender'] = $scope['gender'];
            }

            $password = Hash::make($validated['password'] ?? 'secret123');

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? 'student-'.$validated['student_id'].'@example.test',
                'student_id' => $validated['student_id'],
                'role' => 'student',
                'password' => $password,
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('student');
            }

            DB::table('students')->insert(
                $this->studentPayload($validated, $user->id, $password)
            );

            return response()->json(
                $this->findStudent($validated['student_id']),
                201,
                [],
                JSON_UNESCAPED_UNICODE
            );
        });
    }

    public function update(Request $request, $studentId)
    {
        if (! SchoolAccess::canManageSchool($request->user())) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $user = User::where('student_id', $studentId)->first();

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.($user?->id ?? 'NULL'),
            'password' => 'nullable|string|min:6',
            'class' => 'nullable|string|max:100',
            'section' => 'nullable|string|max:100',
            'grade_level' => 'nullable|string|max:100',
            'stage' => 'nullable|string|max:100',
            'gender' => 'nullable|in:male,female,ذكر,أنثى',
            'status' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'parent_contact' => 'nullable|string|max:100',
            'nationality' => 'nullable|string|max:100',
            'gpa' => 'nullable|numeric',
        ]);

        return DB::transaction(function () use ($validated, $studentId, $user) {
            $student = Student::where('student_id', $studentId)->firstOrFail();
            if (! $this->studentIsVisibleToUser($student, request()->user())) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $scope = SchoolAccess::supervisorScope(request()->user());
            if ($scope) {
                $validated['stage'] = $scope['stage'];
                $validated['gender'] = $scope['gender'];
            }

            $password = isset($validated['password']) ? Hash::make($validated['password']) : null;

            if ($user) {
                $userPayload = array_filter([
                    'name' => $validated['name'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'password' => $password,
                ], fn ($value) => $value !== null);

                if ($userPayload !== []) {
                    $user->update($userPayload);
                }
            }

            $studentPayload = $this->studentPayload($validated, $user?->id, $password, false);
            if ($studentPayload !== []) {
                DB::table('students')
                    ->where('student_id', $student->student_id)
                    ->update($studentPayload);
            }

            return response()->json($this->findStudent($student->student_id), 200, [], JSON_UNESCAPED_UNICODE);
        });
    }

    public function destroy($studentId)
    {
        if (! SchoolAccess::canManageSchool(request()->user())) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return DB::transaction(function () use ($studentId) {
            $student = Student::where('student_id', $studentId)->firstOrFail();
            if (! $this->studentIsVisibleToUser($student, request()->user())) {
                return response()->json(['message' => 'Forbidden'], 403);
            }

            $user = User::where('student_id', $studentId)->first();

            $student->delete();
            $user?->delete();

            return response()->json(['message' => 'Student deleted successfully']);
        });
    }

    private function studentsQuery()
    {
        return Student::query()
            ->leftJoin('users', 'users.student_id', '=', 'students.student_id')
            ->select('students.*', 'users.email as user_email')
            ->orderBy('students.student_id');
    }

    private function findStudent(int|string $studentId)
    {
        return $this->studentsQuery()
            ->where('students.student_id', $studentId)
            ->first();
    }

    private function studentIsVisibleToUser(Student $student, ?User $user): bool
    {
        if (SchoolAccess::isAdmin($user)) {
            return true;
        }

        $scope = SchoolAccess::supervisorScope($user);
        if (! $scope) {
            return false;
        }

        $stage = (string) $student->stage;
        $gender = (string) $student->gender;

        return in_array($stage, [$scope['stage'], SchoolAccess::arabicStage($scope['stage'])], true)
            && in_array($gender, [$scope['gender'], SchoolAccess::arabicGender($scope['gender'])], true);
    }

    private function studentPayload(array $data, ?int $userId, ?string $password = null, bool $withDefaults = true): array
    {
        $payload = [
            'student_id' => $data['student_id'] ?? null,
            'user_id' => $userId,
            'name' => $data['name'] ?? null,
            'password' => $password,
            'class' => $data['class'] ?? null,
            'section' => $data['section'] ?? null,
            'grade_level' => $data['grade_level'] ?? null,
            'stage' => $data['stage'] ?? null,
            'gender' => $data['gender'] ?? null,
            'status' => $data['status'] ?? null,
            'address' => $data['address'] ?? null,
            'parent_contact' => $data['parent_contact'] ?? null,
            'nationality' => $data['nationality'] ?? null,
            'gpa' => $data['gpa'] ?? null,
            'profile_photo' => $withDefaults ? 'default.png' : null,
            'enrollment_date' => $withDefaults ? now()->toDateString() : null,
            'created_at' => $withDefaults ? now() : null,
            'updated_at' => now(),
        ];

        if ($withDefaults) {
            $payload['gender'] ??= 'male';
            $payload['status'] ??= 'active';
            $payload['address'] ??= '';
            $payload['parent_contact'] ??= '';
            $payload['nationality'] ??= '';
            $payload['class'] ??= '';
            $payload['section'] ??= '';
            $payload['grade_level'] ??= $payload['class'];
            $payload['stage'] ??= '';
        }

        $columns = Schema::getColumnListing('students');

        return array_filter(
            array_intersect_key($payload, array_flip($columns)),
            fn ($value) => $value !== null
        );
    }
}
