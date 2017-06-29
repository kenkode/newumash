<style type="text/css">
#wrap{
    margin-top: 90px !important;
    position: fixed;
    top: 0;
    z-index: 10000;
    border-radius: 0 0 0.5em 0.5em;
}
</style>

 <nav class="navbar-default navbar-static-side" id="wrap" role="navigation">
    
           


            <div class="sidebar-collapse">

                <ul class="nav" id="side-menu">

                    <li>
                        <a href="{{ URL::to('payrollReports/selectPeriod') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Monthly Payslips</a>
                    </li>

                    <li>
                        <a href="{{ URL::to('payrollReports/selectSummaryPeriod') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Payroll Summary</a>
                    </li>

                    <li>
                        <a href="{{ URL::to('payrollReports/selectRemittancePeriod') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Pay Remittance</a>
                    </li>

                    <li>
                        <a href="{{ URL::to('payrollReports/selectEarning') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Earning Report</a>
                    </li>  

                    <li>
                        <a href="{{ URL::to('payrollReports/selectAllowance') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Allowance Report</a>
                    </li>  

                    <li>
                        <a href="{{ URL::to('payrollReports/selectnontaxableincome') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Non Taxable Income Report</a>
                    </li>  

                    <li>
                        <a href="{{ URL::to('payrollReports/selectDeduction') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Deduction Report</a>
                    </li> 

                    <li>
                        <a href="{{ URL::to('payrollReports/selectRelief') }}" target="_blank"><i class="glyphicon glyphicon-file fa-fw"></i> Relief Report</a>
                    </li>  

                    
                </ul>
                <!-- /#side-menu -->
            </div>
            <!-- /.sidebar-collapse -->
        </nav>
        <!-- /.navbar-static-side -->