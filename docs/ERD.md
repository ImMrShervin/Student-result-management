# Entity-Relationship Diagram

```mermaid
erDiagram
    USERS ||--o| STUDENTS : "has one"
    USERS ||--o| TEACHERS : "has one"
    USERS ||--o{ ROLES : "assigned (spatie)"

    FACULTIES ||--o{ DEPARTMENTS : "contains"
    FACULTIES ||--o| USERS : "dean"
    DEPARTMENTS ||--o| USERS : "head"
    DEPARTMENTS ||--o{ TEACHERS : "employs"
    DEPARTMENTS ||--o{ STUDENTS : "admits"
    DEPARTMENTS ||--o{ COURSES : "offers"

    ACADEMIC_YEARS ||--o{ SEMESTERS : "contains"

    COURSES ||--o{ COURSE_SECTIONS : "instances"
    COURSES ||--o{ COURSE_PREREQUISITES : "requires"
    SEMESTERS ||--o{ COURSE_SECTIONS : "hosts"
    TEACHERS ||--o{ COURSE_SECTIONS : "teaches"

    STUDENTS ||--o{ ENROLLMENTS : "enrolls"
    COURSE_SECTIONS ||--o{ ENROLLMENTS : "receives"
    SEMESTERS ||--o{ ENROLLMENTS : "occurs in"
    ENROLLMENTS ||--o| GRADES : "graded by"

    STUDENTS ||--o{ SEMESTER_GPAS : "snapshots"
    STUDENTS ||--o{ TRANSCRIPTS : "issued"
    SEMESTERS ||--o{ SEMESTER_GPAS : "aggregates"

    USERS ||--o{ ANNOUNCEMENTS : "authors"
    USERS ||--o{ NOTIFICATIONS : "receives"
    USERS ||--o{ ACTIVITY_LOG : "causes"

    USERS {
        bigint   id PK
        string   first_name
        string   last_name
        string   email UK
        string   phone
        string   national_id UK
        string   code UK
        enum     gender
        date     birth_date
        bool     is_active
        string   locale
        datetime last_login_at
    }
    STUDENTS {
        bigint   id PK
        bigint   user_id FK
        bigint   department_id FK
        bigint   faculty_id FK
        string   student_number UK
        smallint entry_year
        tinyint  current_semester
        decimal  current_gpa
        decimal  cumulative_gpa
        smallint credits_passed
        smallint credits_required
        string   academic_status
    }
    TEACHERS {
        bigint   id PK
        bigint   user_id FK
        bigint   department_id FK
        string   employee_code UK
        string   office
        string   academic_rank
        date     hired_on
    }
    COURSES {
        bigint   id PK
        bigint   department_id FK
        string   code UK
        string   title
        text     description
        tinyint  theory_credit
        tinyint  practical_credit
    }
    COURSE_SECTIONS {
        bigint   id PK
        bigint   course_id FK
        bigint   semester_id FK
        bigint   teacher_id FK
        string   section_code
        smallint capacity
        smallint enrolled_count
    }
    ENROLLMENTS {
        bigint   id PK
        bigint   student_id FK
        bigint   course_section_id FK
        bigint   semester_id FK
        string   status
        datetime enrolled_at
        datetime decided_at
    }
    GRADES {
        bigint   id PK
        bigint   enrollment_id FK UK
        decimal  attendance
        decimal  assignment
        decimal  quiz
        decimal  project
        decimal  midterm
        decimal  practical
        decimal  final_exam
        decimal  total_score
        string   letter_grade
        decimal  gpa_points
        bool     is_published
        datetime published_at
    }
    SEMESTER_GPAS {
        bigint   id PK
        bigint   student_id FK
        bigint   semester_id FK
        decimal  semester_gpa
        decimal  cumulative_gpa
        smallint credits_attempted
        smallint credits_earned
        string   academic_status
    }
    TRANSCRIPTS {
        bigint   id PK
        bigint   student_id FK
        string   verification_code UK
        string   pdf_path
        decimal  cumulative_gpa
        smallint credits_earned
        json     payload
        datetime generated_at
    }
```

## Cardinality summary
- 1 Faculty → many Departments
- 1 Department → many (Teachers, Students, Courses)
- 1 Course → many CourseSections (per Semester)
- 1 Student ↔ many CourseSections via Enrollments
- 1 Enrollment → 0..1 Grade
- 1 Student → many SemesterGpas (one per semester attended)
- 1 Student → many Transcripts (versioned)
