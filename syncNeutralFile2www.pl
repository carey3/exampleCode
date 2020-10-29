#!/tool/pandora64/bin/perl
# /tool/pandora64/bin/perl5.18.1
use strict; 
no warnings 'once'; 
use English;

use Cwd;
use Date::Manip::Date;
use File::Basename;
#se File::Util;
use File::stat;
use File::Copy;
use File::Path qw(make_path);
use File::Spec;
use File::Spec::Functions qw(catdir);
use FindBin qw($Bin);
use Sys::Hostname;
use Term::ReadLine;
use Term::UI;
use XML::LibXML;

my @targets = qw( 
        /tool/mdp/neutralFileXML
);
#       /tool/mdp/www_clynch3/neutralFileXML

if($ARGV[0]) {
   print "$ARGV[0]\n\n";
   @targets = $ARGV[0]
}

my @sources = ` ls -1 /gtofilesystem/tws/FTRF*/neutralfile* ` ;

my $fn = ""; 
my $tmodTime = 9999;
my $smodTime = 9999;
my $tageTime = 9999;
my $sageTime = 9999;
my $copyit;
foreach my $src(@sources) {
    $src =~ s/^\.\///;chomp($src);
    if($src =~ /\/(neutral\w*)/){ $fn = $1 ; } 
    if(-e $src){
       $smodTime = -M $src;
       $sageTime = -A $src;
    }else{
       $smodTime = -2; 
    }   
#   print "$src";

    foreach my $target(@targets) {
       my $tfn = "$target/$fn";
       if(-e $tfn){
          $tmodTime = -M $tfn;
          $tageTime = -A $tfn;
       }else{
          $tmodTime = -1; 
       }
#      print "\t $tfn";
#      if($smodTime < $tmodTime){$copyit = "yes";}else{$copyit = "no";}
#      printf( "\t\t%.1f\t%.1f\t\t%.1f\t%.1f\t%s\n",$smodTime,$tmodTime,$sageTime,$tageTime,$copyit);
       if($smodTime < $tmodTime || !-e $tfn){
          print "\n\tcopy $src $tfn\n\n";
          my $x = `/tool/pandora/bin/cp -p $src $tfn`;
          #copy($src, $tfn);
          #utime($sageTime, $smodTime, $tfn);
       }
    }   
}
