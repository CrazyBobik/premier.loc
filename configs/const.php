<?php
class constant{
     
        static public $catalog = array(
		
                        '1' => array('title'=>'��������',
									 'sub'=>array('24'=>array('title'=>'��������'),
												  '26'=>array('title'=>'�������� � ���������'),
												  '25'=>array('title'=>'�������')
												 )												 
						
						);
						'3' => array('title'=>'����',
									 'sub'=>array('1'=>array('title'=>'���'),
												  '2'=>array('title'=>'����� ����'),
												  '3'=>array('title'=>'����'),
												  '27'=>array('title'=>'������')
												  )
						
						);
						'5' => array('title'=>'������',
									 'sub'=>array('4'=>array('title'=>'����� � �������� ���������'),
												  '5'=>array('title'=>'��������� �������'),
												  '6'=>array('title'=>'�������� ������� �����'),
												  '7'=>array('title'=>'����� �� �������')
												  )
						
						);
						'6' => array('title'=>'������������ ������������',
									 'sub'=>array('13'=>array('title'=>'��� ����'),
												  '14'=>array('title'=>'������� ���������'),
												  '15'=>array('title'=>'������'),
												  '16'=>array('title'=>'������� ������'),
												  '17'=>array('title'=>'���� ������'),
												  '18'=>array('title'=>'�����, ���������'),
												  '19'=>array('title'=>'����� �����'),
												  '20'=>array('title'=>'����, ��������'),
												  '21'=>array('title'=>'���������������� ���������'),
												  '22'=>array('title'=>'�������� �������'),
												  '23'=>array('title'=>'��������� ���������'),
												  )
												  
						);
						'8' => array('title'=>'��������� �������',
									 'sub'=>array('12'=>array('title'=>'������� ��� ����� ���������'),
												  '8'=>array('title'=>'����� ��������-����������� ����������'),
												  '9'=>array('title'=>'����� �������������� ����������'),
												  '10'=>array('title'=>'����� ��������������������� ����������'),
												  '11'=>array('title'=>'����� ������������� ����������')
												  )
						
						);
						'13' => array('title'=>'����� � ���������',
									 'sub'=>array('28'=>array('title'=>'����� � �����'),
												  '29'=>array('title'=>'�����������')
												  )
						
						);
						
		);
		
		static public $dopType = array(
		
                        '1' => array('dop_walls','dop_val2','dop_val4','dop_val5','dop_state','dop_proposal','dop_obmen_b');											 
						
						'3' => array('dop_walls','dop_val2','dop_val4','dop_val5','dop_state','dop_proposal','dop_val8','dop_val9','dop_val10','dop_obmen_b');
						
						'5' => array('dop_nazn','dop_obmen_b');
						
						'6' => array('dop_val4_kom','dop_tob','dop_proposal','dop_obmen_b');
						
						'8' => array('dop_proposal','dop_obmen_b');
						
						'13' => array('dop_obmen_b');
						
		);
		
		static public $dopSubtype = array(
		
                        '2' => array('dop_walls','dop_val2','dop_val4','dop_val5','dop_state','dop_proposal','dop_val8');
						
		);
                    
        static public $mysqlDump = array(
       
                        'link'=>'testdump',
                        'secureTokenArg'=>'token', 
                        'secureToken'=>'elinokoll786', 
                        'insertRecordsCount'=>50
			           
					 ); 
                     
               
 
}