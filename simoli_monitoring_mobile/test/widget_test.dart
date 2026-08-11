import 'package:flutter_test/flutter_test.dart';
import 'package:simoli_monitoring_mobile/main.dart';

void main() {
  testWidgets('Operator App smoke test', (WidgetTester tester) async {
    await tester.pumpWidget(const SimoliMonitoringApp());
    expect(find.text('SIMOLI OPERATOR LAPANGAN'), findsOneWidget);
  });
}
