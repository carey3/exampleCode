#!/usr/bin/perl
use strict;

use DateTime;
use Time::Piece;
use Time::Seconds;

    my $order;               #set up an object for later.
    my $format_ymd = '%Y-%m-%d';
    my $format_mdy = '%m/%d/%y';
    my $today = DateTime->now;
    print ref($today)."\n\n";

    my $tp0  = $today->strftime($format_ymd); #strips off garbage that localtime doesn't like.
    print "time before: $tp0 \n";

    my $tp1 = localtime->strptime($tp0, $format_ymd) - ONE_HOUR * 24 * 7;
    my $tp2 = localtime->strptime($tp0, $format_ymd) + ONE_DAY * 7;
    my $tp3 = localtime->strptime($tp0, $format_ymd) + ONE_WEEK * 2;

    print "time -7 days  $tp1\n";
    print "time +7 days  $tp2\n";
    print "time +2 weeks $tp3\n\n";

    $order->{'due_date'} = $tp1->strftime($format_ymd);
    print "time -7 days  after format $order->{due_date}\n";

    $order->{'due_date'} = $tp2->strftime($format_mdy);
    print "time +7 days  after format $order->{due_date}\n";

    $order->{'due_date'} = $tp3->strftime($format_mdy);
    print "time +2 weeks after format $order->{due_date}\n";
