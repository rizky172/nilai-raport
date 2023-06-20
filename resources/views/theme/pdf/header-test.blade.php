<!DOCTYPE html
  PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <!-- Help to test using HTML view -->
    <style><?php include(resource_path('/css/pdf.css')); ?></style>

    {{-- Set your paper type --}}
    @section('paper')
        @include('theme.partials.paper')
    @show
</head>
<body>
{{-- include header letter you want --}}
{{-- Default header --}}
@include('theme.partials.header')

{{-- Dot Matrix --}}
@include('theme.partials.header', [ 'type' => 'dot-matrix' ])

{{-- Logo & Title/Salary Slip --}}
@include('theme.partials.header', [ 'type' => 'salary-slip', 'title' => 'Title', 'sub_title' => 'Sub Title' ])
</body>
</html>