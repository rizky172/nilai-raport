-- Create history transaction view
drop view if exists view_report_value;
create view view_report_value as
    select rv.teacher_id, rv.class_id, rv.major_id, rv.lesson_id, rv.semester_id, rvd.student_id, rvd.value
        from report_value rv
        join report_value_detail rvd on rvd.report_value_id = rv.id;
