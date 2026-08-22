<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Http\Request;
use League\Csv\Reader;

class QuestionImportController extends Controller
{
    public function showUploadForm()
    {
        return view('admin.questions.import');
    }

    public function preview(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);

        $csv = Reader::createFromPath($request->file('csv_file')->getPathname(), 'r');
        $csv->setHeaderOffset(0);

        $subjects = Subject::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);
        $topics = Topic::pluck('id', 'name')->mapWithKeys(fn ($id, $name) => [strtolower(trim($name)) => $id]);

        $validOptions = ['A', 'B', 'C', 'D'];
        $validDifficulties = ['easy', 'medium', 'hard'];
        $validCognitiveLevels = ['recall', 'understanding', 'application'];

        $rows = [];
        $rowNumber = 1;

        foreach ($csv->getRecords() as $record) {
            $rowNumber++;
            $errors = [];

            $question = trim($record['question'] ?? '');
            $optionA = trim($record['option_a'] ?? '');
            $optionB = trim($record['option_b'] ?? '');
            $optionC = trim($record['option_c'] ?? '');
            $optionD = trim($record['option_d'] ?? '');
            $correctOption = strtoupper(trim($record['correct_answer'] ?? $record['correct_option'] ?? ''));
            $subjectName = trim($record['subject'] ?? '');
            $topicName = trim($record['topic'] ?? '');
            $difficulty = strtolower(trim($record['difficulty'] ?? 'medium'));
            $cognitiveLevel = strtolower(trim($record['cognitive_level'] ?? ''));
            $explanation = trim($record['explanation'] ?? '');
            $source = trim($record['source'] ?? '');
            $year = trim($record['year'] ?? '');

            if ($question === '') {
                $errors[] = 'Question text is empty.';
            }
            if ($optionA === '') $errors[] = 'Option A is empty.';
            if ($optionB === '') $errors[] = 'Option B is empty.';
            if ($optionC === '') $errors[] = 'Option C is empty.';
            if ($optionD === '') $errors[] = 'Option D is empty.';

            if (! in_array($correctOption, $validOptions)) {
                $errors[] = "Correct answer must be A, B, C, or D, but found '{$correctOption}'.";
            }

            $subjectId = $subjects[strtolower($subjectName)] ?? null;
            if (! $subjectId) {
                $errors[] = "Subject '{$subjectName}' does not exist. Please add it first.";
            }

            $topicId = null;
            if ($topicName !== '') {
                $topicId = $topics[strtolower($topicName)] ?? null;
                if (! $topicId) {
                    $errors[] = "Topic '{$topicName}' does not exist. Please add it first.";
                }
            }

            if (! in_array($difficulty, $validDifficulties)) {
                $errors[] = "Difficulty must be easy, medium, or hard, but found '{$difficulty}'.";
            }

            if ($cognitiveLevel !== '' && ! in_array($cognitiveLevel, $validCognitiveLevels)) {
                $errors[] = "Cognitive level must be recall, understanding, or application, but found '{$cognitiveLevel}'.";
            }

            $rows[] = [
                'row_number' => $rowNumber,
                'question' => $question,
                'option_a' => $optionA,
                'option_b' => $optionB,
                'option_c' => $optionC,
                'option_d' => $optionD,
                'correct_option' => $correctOption,
                'subject_id' => $subjectId,
                'subject_name' => $subjectName,
                'topic_id' => $topicId,
                'topic_name' => $topicName,
                'difficulty' => in_array($difficulty, $validDifficulties) ? $difficulty : 'medium',
                'cognitive_level' => in_array($cognitiveLevel, $validCognitiveLevels) ? $cognitiveLevel : null,
                'explanation' => $explanation,
                'source' => $source,
                'year' => $year,
                'errors' => $errors,
                'is_valid' => empty($errors),
            ];
        }

        session(['import_rows' => $rows]);

        $validCount = collect($rows)->where('is_valid', true)->count();
        $errorCount = collect($rows)->where('is_valid', false)->count();

        return view('admin.questions.import-preview', compact('rows', 'validCount', 'errorCount'));
    }

    public function confirm(Request $request)
    {
        $rows = session('import_rows', []);

        $imported = 0;

        foreach ($rows as $row) {
            if (! $row['is_valid']) {
                continue;
            }

            Question::create([
                'subject_id' => $row['subject_id'],
                'topic_id' => $row['topic_id'],
                'question' => $row['question'],
                'option_a' => $row['option_a'],
                'option_b' => $row['option_b'],
                'option_c' => $row['option_c'],
                'option_d' => $row['option_d'],
                'correct_option' => $row['correct_option'],
                'difficulty' => $row['difficulty'],
                'cognitive_level' => $row['cognitive_level'],
                'explanation' => $row['explanation'] ?: null,
                'source' => $row['source'] ?: null,
                'year' => $row['year'] ?: null,
                'status' => 'draft',
            ]);

            $imported++;
        }

        session()->forget('import_rows');

        return redirect()->route('admin.questions.index')->with('success', "{$imported} questions imported successfully as drafts. Review and activate them.");
    }
}
