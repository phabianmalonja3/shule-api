<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annual Examination Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            background: #fff;
            margin: 0;
            padding: 0;
            font-size: 12px; /* Reduce default font size */
        }

        .container {
            width: 90%; 
            margin: auto;
            padding: 20px;
            border: 1px solid #ddd;
            background: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 15px; /* Reduce header margin */
        }

        .header img {
            position: absolute;
            width: 60px;
        }

        .header .left-logo {
            top: 10px;
            left: 10px;
        }

        .header .right-logo {
            top: 10px;
            right: 10px;
        }

        .header h1 {
            margin: 0;
            font-size: 16px; /* Slightly smaller header font */
            font-weight: bold;
        }

        .header p {
            margin: 2px 0;
            font-size: 12px; 
        }

        .section {
            margin-bottom: 15px; /* Reduce section margin */
        }

        .section p {
            margin: 5px 0;
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px; /* Reduce table margin */
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 6px; /* Slightly reduce cell padding */
            text-align: center;
            font-size: 11px; 
        }

        table th {
            background: #f5f5f5;
            font-weight: bold;
        }

        .footer {
            margin-top: 15px; /* Reduce footer margin */
        }

        .footer .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .footer .signatures div {
            text-align: center;
            width: 45%;
        }

        .footer p {
            font-size: 11px;
            line-height: 1.4;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header" style="text-align: center; position: relative; margin-bottom: 20px;">
            {{-- <img src="{{ public_path('images/government_logo.png') }}" alt="Government Logo" style="position: absolute; left: 0; top: 0; width: 80px;"> --}}
            {{-- <img src="{{ public_path($school->logo) }}" alt="School Logo" style="position: absolute; right: 0; top: 0; width: 80px;"> --}}
            <div style="margin-top: 50px;">
                <p style="font-size: 18px; font-weight: bold;">THE UNITED REPUBLIC OF TANZANIA</p>
                <p style="font-size: 14px;">PRESIDENT'S OFFICE, REGIONAL ADMINISTRATIVE AND LOCAL GOVERNMENT</p>
                <p style="font-size: 14px; ">{{strtoupper($school->district) }} MUNICIPAL COUNCIL</p>
                <p style="font-size: 16px; font-weight: bold;">{{ $school->name }}</p>
                <p>{{ $school->address ?? 'No Address' }}</p>
                <p>Phone: {{ $school->phone ?? 'No Phone' }} | Email: {{ $school->email ?? ' ' }}</p>
                <p><em>{{ $school->motto ?? 'No school Motto' }}</em></p>
                <h2>{{ strtoupper($marks->first()['exam_type']) }} EXAMINATION REPORT, DECEMBER 2024</h2>

            </div>
        </div>

        <div class="section">
            <p><strong>Pupil's Name:</strong> {{ $student->user->name }}</p>
            <p><strong>Prem No :</strong> {{ $student->reg_number }}</p>
            <p><strong>Class :</strong> {{ $student->schoolClass->name }}</p>
            <p><strong>Year :</strong> {{ $year }}</p> 
            <p><strong>Key:</strong> 
                @foreach ($scales as $gradeScale)
                    {{ $gradeScale->grade }} = {{ $gradeScale->min_marks }} - {{ $gradeScale->max_marks }}
                    @if (!$loop->last), @endif
                @endforeach
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th>SC</th>
                    <th>Subject Name</th>
                    <th>Mark</th>
                    <th>Grade</th>
                    <th>PST</th>
                    <th>Teacher's Comment</th>
                    <th>Subject Teacher's Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $index => $mark)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $mark['subject'] }}</td>
                    <td>{{ $mark['marks'] }}</td>
                    <td>{{ $mark['grade'] }}</td>
                    <td>{{ $mark['position'] }}</td>
                    <td>{{ $mark['remarks'] }}</td>
                    <td>{{ $mark['teacher'] }}</td>
                    
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="section">
            <p><strong>Total:</strong> 83 | <strong>Grade:</strong> B | <strong>Out of:</strong> {{ $totalStudents }} Pupils</p>
        </div>

        <div class="footer">
            <p><strong>School Terms:</strong> The school will close on 20th December 2024 and re-open on 6th January 2025.</p>
            <p><strong>School Fees:</strong> Please clear any outstanding school fees before your child reports to school in January 2025. Payment should be made to:</p>
            <p><strong>NMB Account:</strong> 205038000041</p>
            <p>Submit the bank pay-in slip to the headteacher by 10th January 2025. Students will not be allowed to attend classes if fees remain unpaid.</p>
            <table style="width: 100%; margin-top: 30px; text-align: center; border: none; border-collapse: collapse;">
                <tr>
                    <td style="width: 50%; border: none;">
                        <p>..............................</p>
                        <p><strong>{{ $teacherName }}</strong></p>
                        <p>Class Teacher</p>
                    </td>
                    <td style="width: 50%; border: none;">
                        <p>..............................</p>
                        <p><strong>{{ $headTeacher->name }}</strong></p>
                        <p>Head Teacher</p>
                    </td>
                </tr>
            </table>
            
        </div>
    </div>
</body>
</html>
