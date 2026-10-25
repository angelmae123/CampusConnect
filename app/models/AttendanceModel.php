<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

/**
 * Model: AttendanceModel
 * 
 * Automatically generated via CLI.
 */
class AttendanceModel extends Model {
    protected $table = 'attendance';
    protected $primary_key = 'attendance_id';
    protected $fillable = ['attendance_id','event_reg_id','scanned_at','scanned_by','status'];
    protected $guarded = ['attendance_id', 'scanned_at'];

    public function __construct()
    {
        parent::__construct();
    }
}