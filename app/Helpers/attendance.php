<?php

use App\Models\EmployeeDailyAttendance;
use App\Models\EmployeeShift;

function check_in_employee($employee_id, $date_check)
{
    $check_in_attendance_date_time = '';
    $date_time_to_return = '';

    $employee_shift = EmployeeShift::where('employee_id', $employee_id)->where('is_default', 0)->where('from_date', '<=', $date_check)->where('to_date', '>=', $date_check)->with(['shift' => function ($query) {
        $query->select('id', 'from', 'to', 'is_day_changed');
    }])->orderBy('from_date', 'DESC')->orderBy('to_date', 'ASC')->first();


    if (empty($employee_shift)) {
        $employee_shift = EmployeeShift::where('employee_id', $employee_id)->where('is_default', 1)->where('from_date', '<=', $date_check)->where('to_date', '>=', $date_check)->with(['shift' => function ($query) {
            $query->select('id', 'from', 'to', 'is_day_changed');
        }])->first();
    }


    $check_in_from_shift = $employee_shift->shift->from;
    $check_in_to_shift = $employee_shift->shift->to;
    $check_in_is_day_changed = $employee_shift->shift->is_day_changed;


    if ($check_in_is_day_changed != '') {

        if ($check_in_is_day_changed == 0) {
            $check_in_attendance_time = '';

            $employee_daily_attendance = EmployeeDailyAttendance::where('employee_id', $employee_id)->whereDate('attendance_datetime', $date_check)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_IN)->first();

            $check_in_attendance_time = $employee_daily_attendance->attendance_datetime;
            $date_time_to_return = $employee_daily_attendance->attendance_datetime;

            $check_in_attendance_time_new = date('H:i:s', strtotime($check_in_attendance_time));

            if ($check_in_attendance_time == '') // Check Previous 04 Hours in Check-in time
            {
                $check_in_attendance_date_time = $date_check . " " . $check_in_from_shift;
                $check_in_attendance_date_time_other = strtotime($check_in_attendance_date_time) - 14400;
                $check_in_attendance_date_time_other = date("Y-m-d H:i:s", $check_in_attendance_date_time_other);

                $employee_daily_attendance_new = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '>=', $check_in_attendance_date_time_other)->where('attendance_datetime', '<=', $check_in_attendance_date_time)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_IN)->first();

                if (!empty($employee_daily_attendance)) {
                    $check_in_attendance_time_new = date('H:i:s', strtotime($employee_daily_attendance_new->attendance_datetime));
                    $date_time_to_return = $employee_daily_attendance_new->attendance_datetime;
                } else {
                    $check_in_attendance_time_new = '';
                }
            }
        }
        if ($check_in_is_day_changed == 1) {
            $check_in_attendance_time = '';

            $employee_daily_attendance = EmployeeDailyAttendance::where('employee_id', $employee_id)->whereDate('attendance_datetime', $date_check)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_IN)->first();

            $check_in_attendance_time = $employee_daily_attendance->attendance_datetime;
            $date_time_to_return = $employee_daily_attendance->attendance_datetime;
            $check_in_attendance_time_new = date('H:i:s', strtotime($check_in_attendance_time));

            if ($check_in_attendance_time == '') {
                $check_time = '00:01:00';

                $next_date = date('Y-m-d', strtotime('+1 day', strtotime($date_check)));
                $employee_daily_attendance_new = EmployeeDailyAttendance::where('employee_id', $employee_id)->whereDate('attendance_datetime', $next_date)->whereTime('attendance_datetime', '>=', $check_time)->whereTime('attendance_datetime', '<=', $check_in_to_shift)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_IN)->orderBy('id', 'DESC')->first();

                if (!empty($employee_daily_attendance_new)) {
                    $check_in_attendance_time_new = date('H:i:s', strtotime($employee_daily_attendance_new->attendance_datetime));
                    $date_time_to_return = $employee_daily_attendance_new->attendance_datetime;
                } else {
                    $check_in_attendance_time_new = '';
                }
            }
        }
    }

    $check_in_array = array($check_in_attendance_time_new, $date_time_to_return);
    return $check_in_array;
}



function check_out_employee($employee_id, $date_check)
{
    $check_out_attendance_date_time = '';
    $check_out_attendance_date_new = '';
    $check_out_attendance_time_new = '';
    $date_time_to_return = '';
    // $query23 = "SELECT b.from_shift,b.to_shift,b.is_day_changed from employee_global_shift_assign as a inner join employee_shift as b on a.employee_shift_id=b.id where a.employee_id=" . $employee_id . " and a.is_default=0 and '" . $date_check . "' >= a.from_date and '" . $date_check . "' <= a.to_date order by a.from_date DESC,a.to_date ASC";
    // $result23 = mysql_query($query23);
    // if (mysql_num_rows($result23) == 0) {
    //     $query23 = "SELECT b.from_shift,b.to_shift,b.is_day_changed from employee_global_shift_assign as a inner join employee_shift as b on a.employee_shift_id=b.id where a.employee_id=" . $employee_id . " and a.is_default=1 and '" . $date_check . "' >= a.from_date and '" . $date_check . "' <= a.to_date";
    //     $result23 = mysql_query($query23);
    // }

    // $data23 = mysql_fetch_array($result23);
    // $check_out_from_shift = $data23['from_shift'];
    // $check_out_to_shift = $data23['to_shift'];
    // $check_out_is_day_changed = $data23['is_day_changed'];

    $employee_shift = EmployeeShift::where('employee_id', $employee_id)->where('is_default', 0)->where('from_date', '<=', $date_check)->where('to_date', '>=', $date_check)->with(['shift' => function ($query) {
        $query->select('id', 'from', 'to', 'is_day_changed');
    }])->orderBy('from_date', 'DESC')->orderBy('to_date', 'ASC')->first();


    if (empty($employee_shift)) {
        $employee_shift = EmployeeShift::where('employee_id', $employee_id)->where('is_default', 1)->where('from_date', '<=', $date_check)->where('to_date', '>=', $date_check)->with(['shift' => function ($query) {
            $query->select('id', 'from', 'to', 'is_day_changed');
        }])->first();
    }

    $check_out_from_shift = $employee_shift->shift->from;
    $check_out_to_shift = $employee_shift->shift->to;
    $check_out_is_day_changed = $employee_shift->shift->is_day_changed;

    if ($check_out_is_day_changed != '') {

        if ($check_out_is_day_changed == 0) {
            // $query24 = "SELECT attendance_time,count(*) as row_count from employee_attendance where erp_id=" . $check_out_erp_id . " and DATE(attendance_time)='" . $date_check . "' and type=1";
            // $result24 = mysql_query($query24);
            // $data24 = mysql_fetch_array($result24);
            // $check_out_attendance_time = $data24['attendance_time'];
            // $date_time_to_return = $data24['attendance_time'];
            // $row_count = $data24['row_count'];

            $row_count = EmployeeDailyAttendance::where('employee_id', $employee_id)->whereDate('attendance_datetime', $date_check)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->count();

            if ($row_count > 1) {
                $check_in_pre = check_in_employee($employee_id, $date_check);
                $check_in_pre = $check_in_pre[1];
                // $query24 = "SELECT attendance_time from employee_attendance where erp_id=" . $check_out_erp_id . " and DATE(attendance_time)='" . $date_check . "' and attendance_time >'" . $check_in_pre . "' and type=1";
                // $result24 = mysql_query($query24);
                // $data24 = mysql_fetch_array($result24);
                // $check_out_attendance_time = $data24['attendance_time'];
                // $check_out_attendance_time_new = date('H:i:s', strtotime($check_out_attendance_time));
                // $date_time_to_return = $data24['attendance_time'];


                $employee_daily_attendance = EmployeeDailyAttendance::where('employee_id', $employee_id)->whereDate('attendance_datetime', $date_check)->where('attendance_datetime', '>', $check_in_pre)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->first();
                $check_out_attendance_time = $employee_daily_attendance->attendance_datetime;
                $check_out_attendance_time_new = date('H:i:s', strtotime($check_out_attendance_time));
                $date_time_to_return = $employee_daily_attendance->attendance_datetime;
            }


            if ($row_count == 1) ///////////////////////////why this
            {
                $today_check_in = check_in_employee($employee_id, $date_check);

                $next_date = date('Y-m-d', strtotime('+1 day', strtotime($date_check)));
                $next_date_check_in = check_in_employee($employee_id, $next_date);
                if ($next_date_check_in[1] != '' && $today_check_in[1] != '') {
                    // $query24 = "SELECT attendance_time from employee_attendance where erp_id=" . $check_out_erp_id . " and attendance_time <'" . $next_date_check_in[1] . "' and attendance_time >'" . $today_check_in[1] . "' and type=1";

                    $query24 = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '<', $next_date_check_in[1])->where('attendance_datetime', '>', $today_check_in[1])->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->first();
                }
                if ($next_date_check_in[1] != '' && $today_check_in[1] == '') {
                    // $query24 = "SELECT attendance_time from employee_attendance where erp_id=" . $check_out_erp_id . " and attendance_time <'" . $next_date_check_in[1] . "' and type=1 order by id DESC Limit 1";

                    $query24 = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '<', $next_date_check_in[1])->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->orderBy('id', 'DESC')->first();
                }
                if ($next_date_check_in[1] == '' && $today_check_in[1] != '') {
                    // $query24 = "SELECT attendance_time from employee_attendance where erp_id=" . $check_out_erp_id . " and attendance_time >'" . $today_check_in[1] . "' and type=1 order by attendance_time";

                    $query24 = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '>', $today_check_in[1])->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->orderBy('attendance_time', 'ASC')->first();
                }

                $check_out_attendance_time = $query24->attendance_datetime;

                $check_out_attendance_time_new = date('H:i:s', strtotime($check_out_attendance_time));
                $date_time_to_return = $query24->attendance_datetime;
            }

            if ($check_out_attendance_time == '') // Check Next 04 Hours in Check-out time
            {
                $next_date = date('Y-m-d', strtotime('+1 day', strtotime($date_check)));
                $time_to_check = '00:00:00';

                $check_out_attendance_date_time = $next_date . " " . $time_to_check;

                $check_out_attendance_date_time_other = strtotime($check_out_attendance_date_time) + 14400;
                $check_out_attendance_date_time_other = date("Y-m-d H:i:s", $check_out_attendance_date_time_other);

                // $query25 = "SELECT attendance_time FROM employee_attendance where erp_id=" . $check_out_erp_id . " and attendance_time>='" . $check_out_attendance_date_time . "' and attendance_time<='" . $check_out_attendance_date_time_other . "' and type=1";
                // $result25 = mysql_query($query25);
                // $data25 = mysql_fetch_array($result25);
                // $check_out_attendance_time_new = date('H:i:s', strtotime($data25['attendance_time']));
                // $date_time_to_return = $data25['attendance_time'];
                // if (mysql_num_rows($result25) == 0) {
                //     $check_out_attendance_time_new = '';
                // }

                $employee_daily_attendance_new = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '>=', $check_out_attendance_date_time)->where('attendance_datetime', '<=', $check_out_attendance_date_time_other)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->first();

                if (!empty($employee_daily_attendance_new)) {
                    $check_out_attendance_time_new = date('H:i:s', strtotime($employee_daily_attendance_new->attendance_datetime));
                    $date_time_to_return = $employee_daily_attendance_new->attendance_datetime;
                } else {
                    $check_out_attendance_time_new = '';
                }
            }
        }
        if ($check_out_is_day_changed == 1) {

            $next_date = date('Y-m-d', strtotime($date_check));
            $previous_date = date('Y-m-d', strtotime('-1 day', strtotime($date_check)));

            // $query24 = "SELECT attendance_time from employee_attendance where erp_id=" . $check_out_erp_id . " and DATE(attendance_time)='" . $next_date . "' and TIME(attendance_time) < '" . $check_out_from_shift . "' and type=1";
            // $result24 = mysql_query($query24);
            // $data24 = mysql_fetch_array($result24);
            $query24 = EmployeeDailyAttendance::where('employee_id', $employee_id)->whereDate('attendance_datetime', $next_date)->whereTime('attendance_datetime', '<', $check_out_from_shift)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->first();

            if (!empty($query24)) {

                $check_out_attendance_time = $query24->attendance_datetime;
                $date_time_to_return = $query24->attendance_datetime;

                $check_out_attendance_time_new = date('H:i:s', strtotime($check_out_attendance_time));
                $check_out_attendance_date_new = $previous_date;
            } else {

                $time_to_check = '23:59:59';

                $check_out_attendance_date_time = $date_check . " " . $time_to_check;
                $date_time_to_check = $date_check . " " . $check_out_from_shift;

                // $query25 = "SELECT attendance_time FROM employee_attendance where erp_id=" . $check_out_erp_id . " and attendance_time >='" . $date_time_to_check . "' and attendance_time<='" . $check_out_attendance_date_time . "' and type=1";
                // $result25 = mysql_query($query25);
                // $data25 = mysql_fetch_array($result25);

                $employee_daily_attendance_new = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '>=', $date_time_to_check)->where('attendance_datetime', '<=', $check_out_attendance_date_time)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->first();
                if (!empty($employee_daily_attendance_new)) {
                    $check_out_attendance_time_new = date('H:i:s', strtotime($employee_daily_attendance_new->attendance_datetime));
                    $date_time_to_return = $employee_daily_attendance_new->attendance_datetime;
                } else {
                    //////////////////Newly added code 26-04-2021///////
                    $date_time_to_check = $previous_date . " " . $check_out_from_shift;
                    $check_out_attendance_date_time = $date_check . " " . $check_out_to_shift;
                    // $query25 = "SELECT attendance_time FROM employee_attendance where erp_id=" . $check_out_erp_id . " and attendance_time >='" . $date_time_to_check . "' and attendance_time<='" . $check_out_attendance_date_time . "' and type=1 order by id DESC Limit 1";

                    $query25 = EmployeeDailyAttendance::where('employee_id', $employee_id)->where('attendance_datetime', '>=', $date_time_to_check)->where('attendance_datetime', '<=', $check_out_attendance_date_time)->where('type', EmployeeDailyAttendance::ATTENDANCE_TYPE_OUT)->first();

                    if (!empty($query25)) {
                        $check_out_attendance_time_new = date('H:i:s', strtotime($query25->attendance_time));
                        $date_time_to_return = $query25->attendance_time;
                        $check_out_attendance_date_new = $previous_date;
                    } else {
                        $check_out_attendance_time_new = '';
                    }
                }
            }
        }
    }

    $check_out_array = array($check_out_attendance_time_new, $date_time_to_return, $check_out_attendance_date_new);
    return $check_out_array;
}





// function holiday_check($employee_id, $date_check)
// {
//     $query72 = "SELECT holiday from employee_global_shift_assign where employee_id=" . $employee_id . " and '" . $date_check . "' >= from_date and '" . $date_check . "' <= to_date and is_default=0";
//     $result72 = mysql_query($query72);
//     if (mysql_num_rows($result72) == 0) {
//         $query72 = "SELECT holiday from employee_global_shift_assign where employee_id=" . $employee_id . " and '" . $date_check . "' >= from_date and '" . $date_check . "' <= to_date and is_default=1";
//         $result72 = mysql_query($query72);
//     }

//     $data72 = mysql_fetch_array($result72);
//     $holiday = $data72['holiday'];
//     // $holiday_check=explode(",", $holiday);
//     return $holiday;

//     //$day = date('l',strtotime($date_check));

//     // $count=0;	
//     // for ($i=0; $i < count($holiday_check) ; $i++) 
//     // { 
//     // 	if ($holiday_check[0] == $day) 
//     // 	{
//     // 		$count++;
//     // 	}
//     // }
//     // if ($count> 0) 
//     // {
//     // 	return 1;
//     // }
//     // else
//     // {
//     // 	return 0;
//     // }
// }

// function leave_check($employee_id, $date_check)
// {
//     $query72 = "SELECT id from employee_leaves where employee_id=" . $employee_id . " and '" . $date_check . "' >= fromdate and '" . $date_check . "' <= todate and status!='Cancelled'  and is_hod!=2";
//     $result72 = mysql_query($query72);
//     if (mysql_num_rows($result72) > 0) {
//         return 1;
//     } else {
//         return 0;
//     }
// }
// function leave_check_id($employee_id, $date_check)
// {
//     $query72 = "SELECT id from employee_leaves where employee_id=" . $employee_id . " and '" . $date_check . "' >= fromdate and '" . $date_check . "' <= todate and status!='Cancelled' and is_hod!=2";
//     $result72 = mysql_query($query72);
//     if (mysql_num_rows($result72) > 0) {
//         $data72 = mysql_fetch_array($result72);
//         $leave_id = $data72['id'];
//         return $leave_id;
//     } else {
//         return '';
//     }
// }
