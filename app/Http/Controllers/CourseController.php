<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courseByCategory = $this->courseService->getCourseGroupedByCategory();

        return view('courses.index', compact('courseByCategory'));
    }

    public function details(Course $course)
    {
        $course->load([
            'category',
            'benefits',
            'courseSections.sectionContents'
        ]);

        return view('courses.details', compact('course'));
    }

    public function join(Course $course)
    {
        $studentName = $this->courseService->enrollUser($course);
        $firstSectionAndContent = $this->courseService->getFirstSectionAndContent($course);

        return view('course.success_joined', array_merge(
            compact('courses', 'studentName'), $firstSectionAndContent
        ));
    }
    
    public function learning(Course $course, $contentSectionId, $sectionContentId)
    {
        $learnignData = $this->courseService->getLearningData($course, $contentSectionId, $sectionContentId);

        return view('courses.learning', $learningData);
    }

    public function learning_finished(Course $course)
    {
        return view('courses.learning_finished', compact('course'));
    }

    public function search_course(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);
        
        $keyword = $request->search;

        // Delegate the search logic to the service
        $course = $this->courseService->searchCourse($keyword);

        return view('courses.search', compact('courses', 'keyword'));
    }
}
