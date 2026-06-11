import 'package:collection/collection.dart';
import 'package:dio/dio.dart';
import 'package:flutter/material.dart';
import 'package:rand/helpers/locale_direction.dart';

class DashboardPage extends StatelessWidget {
  final String token;
  final String role;

  const DashboardPage({required this.token, required this.role, super.key});

  bool get isSupervisor => role.toLowerCase().contains('supervisor');

  @override
  Widget build(BuildContext context) {
    final dio = Dio(
      BaseOptions(
        baseUrl: 'http://192.168.1.10:8000/api',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Authorization': 'Bearer $token',
        },
      ),
    );
    final prefix = isSupervisor ? '/supervisor' : '';

    return Directionality(
      textDirection: appTextDirection(context),
      child: DefaultTabController(
        length: isSupervisor ? 6 : 7,
        child: Scaffold(
          backgroundColor: const Color(0xFFF6F8FB),
          appBar: AppBar(
            backgroundColor: const Color(0xFF0F766E),
            foregroundColor: Colors.white,
            title: Text(titleForRole(role)),
            bottom: TabBar(
              isScrollable: true,
              labelColor: Colors.white,
              unselectedLabelColor: const Color(0xFFCCFBF1),
              indicatorColor: Colors.white,
              tabs: [
                Tab(icon: Icon(Icons.groups_outlined), text: 'ط§ظ„ط·ظ„ط§ط¨'),
                Tab(icon: Icon(Icons.grade_outlined), text: 'ط§ظ„ط¹ظ„ط§ظ…ط§طھ'),
                Tab(icon: Icon(Icons.payments_outlined), text: 'ط§ظ„ط¯ظپط¹ط§طھ'),
                Tab(icon: Icon(Icons.directions_bus_outlined), text: 'ط§ظ„ظ†ظ‚ظ„'),
                Tab(icon: Icon(Icons.event_note_outlined), text: 'ط§ظ„ط§ظ…طھط­ط§ظ†ط§طھ'),
                Tab(icon: Icon(Icons.calendar_month_outlined), text: 'ط§ظ„ط¬ط¯ظˆظ„'),
                if (!isSupervisor) const Tab(icon: Icon(Icons.campaign_outlined), text: 'الإعلانات'),
              ],
            ),
          ),
          body: TabBarView(
            children: [
              StudentsTab(dio: dio, role: role),
              ResourceTab(
                dio: dio,
                title: 'ط¥ط¯ط§ط±ط© ط§ظ„ط¹ظ„ط§ظ…ط§طھ',
                endpoint: '$prefix/grades',
                displayKeys: const ['id', 'student_id', 'marks_obtained', 'exam_component_id'],
                fields: const [
                  FieldSpec('student_id', 'ط±ظ‚ظ… ط§ظ„ط·ط§ظ„ط¨', type: TextInputType.number),
                  FieldSpec('exam_component_id', 'ظ…ظƒظˆظ‘ظ† ط§ظ„ط§ظ…طھط­ط§ظ†', type: TextInputType.number),
                  FieldSpec('marks_obtained', 'ط§ظ„ط¹ظ„ط§ظ…ط©', type: TextInputType.number),
                ],
              ),
              ResourceTab(
                dio: dio,
                title: 'ط¥ط¯ط§ط±ط© ط§ظ„ط¯ظپط¹ط§طھ',
                endpoint: '$prefix/payments',
                displayKeys: const ['id', 'student_id', 'amount', 'status', 'due_date'],
                fields: const [
                  FieldSpec('enrollment_id', 'ط±ظ‚ظ… ط§ظ„طھط³ط¬ظٹظ„', type: TextInputType.number),
                  FieldSpec('method', 'ط·ط±ظٹظ‚ط© ط§ظ„ط¯ظپط¹'),
                  FieldSpec('reference', 'ط§ظ„ظ…ط±ط¬ط¹', required: false),
                  FieldSpec('due_date', 'طھط§ط±ظٹط® ط§ظ„ط§ط³طھط­ظ‚ط§ظ‚', required: false),
                ],
              ),
              ResourceTab(
                dio: dio,
                title: 'ط¥ط¯ط§ط±ط© ط§ظ„ظ†ظ‚ظ„',
                endpoint: '/transport-routes',
                displayKeys: const ['id', 'name', 'code', 'active'],
                fields: const [
                  FieldSpec('name', 'ط§ط³ظ… ط§ظ„ط®ط·'),
                  FieldSpec('code', 'ط§ظ„ظƒظˆط¯', required: false),
                  FieldSpec('active', 'ظ†ط´ط· true/false', required: false),
                ],
              ),
              ResourceTab(
                dio: dio,
                title: 'ط¥ط¯ط§ط±ط© ط§ظ„ط¬ط¯ط§ظˆظ„ ط§ظ„ط§ظ…طھط­ط§ظ†ظٹط©',
                endpoint: '$prefix/exams',
                displayKeys: const ['id', 'title', 'exam_code', 'exam_date', 'exam_type'],
                fields: const [
                  FieldSpec('title', 'ط§ظ„ط¹ظ†ظˆط§ظ†'),
                  FieldSpec('exam_code', 'ط§ظ„ظƒظˆط¯'),
                  FieldSpec('subject_id', 'ط§ظ„ظ…ط§ط¯ط©', type: TextInputType.number),
                  FieldSpec('classrooms_id', 'ط§ظ„طµظپ', type: TextInputType.number),
                  FieldSpec('exam_date', 'ط§ظ„طھط§ط±ظٹط®'),
                  FieldSpec('exam_type', 'midterm/final'),
                  FieldSpec('start_time', 'ط§ظ„ط¨ط¯ط§ظٹط©', required: false),
                  FieldSpec('end_time', 'ط§ظ„ظ†ظ‡ط§ظٹط©', required: false),
                  FieldSpec('syllabus', 'ط§ظ„ظ…ظ‚ط±ط±', required: false),
                ],
              ),
              ResourceTab(
                dio: dio,
                title: 'ط¥ط¯ط§ط±ط© ط§ظ„ط¬ط¯ظˆظ„ ط§ظ„ط£ط³ط¨ظˆط¹ظٹ',
                endpoint: isSupervisor ? '/supervisor/timetables' : '/timetables',
                displayKeys: const ['id', 'day', 'subject', 'class_room_id', 'start_time', 'end_time'],
                createAsList: true,
                fields: const [
                  FieldSpec('day', 'ط§ظ„ظٹظˆظ…'),
                  FieldSpec('subject', 'ط§ظ„ظ…ط§ط¯ط©'),
                  FieldSpec('class_room_id', 'ط§ظ„طµظپ', type: TextInputType.number),
                  FieldSpec('start_time', 'ط§ظ„ط¨ط¯ط§ظٹط©', required: false),
                  FieldSpec('end_time', 'ط§ظ„ظ†ظ‡ط§ظٹط©', required: false),
                  FieldSpec('room', 'ط§ظ„ط؛ط±ظپط©', required: false),
                ],
              ),
              if (!isSupervisor)
                ResourceTab(
                  dio: dio,
                  title: 'إدارة الإعلانات',
                  endpoint: '/ads',
                  displayKeys: const ['id', 'content', 'created_at'],
                  fields: const [
                    FieldSpec('content', 'نص الإعلان'),
                  ],
                ),
            ],
          ),
        ),
      ),
    );
  }
}

class StudentsTab extends StatefulWidget {
  final Dio dio;
  final String role;

  const StudentsTab({required this.dio, required this.role, super.key});

  @override
  State<StudentsTab> createState() => _StudentsTabState();
}

class _StudentsTabState extends State<StudentsTab> {
  final search = TextEditingController();
  List<Map<String, dynamic>> rows = [];
  bool loading = true;
  String? error;

  @override
  void initState() {
    super.initState();
    load();
  }

  @override
  void dispose() {
    search.dispose();
    super.dispose();
  }

  Future<void> load() async {
    setState(() {
      loading = true;
      error = null;
    });
    try {
      final res = await widget.dio.get('/students');
      if (!mounted) return;
      setState(() => rows = extractList(res.data));
    } on DioException catch (e) {
      if (!mounted) return;
      setState(() => error = messageFromDio(e));
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  List<Map<String, dynamic>> get filtered {
    final q = search.text.trim().toLowerCase();
    if (q.isEmpty) return rows;
    return rows.where((r) => r.values.any((v) => '$v'.toLowerCase().contains(q))).toList();
  }

  Future<void> save([Map<String, dynamic>? row]) async {
    final scope = scopeForRole(widget.role);
    final data = await showDialog<Map<String, dynamic>>(
      context: context,
      builder: (_) => StudentDialog(initial: row, lockedScope: scope),
    );
    if (data == null) return;
    try {
      if (row == null) {
        await widget.dio.post('/students', data: data);
      } else {
        data.remove('student_id');
        if ((data['password'] ?? '').toString().isEmpty) data.remove('password');
        await widget.dio.put('/students/${row['student_id']}', data: data);
      }
      await load();
    } on DioException catch (e) {
      snack(messageFromDio(e), true);
    }
  }

  Future<void> delete(Map<String, dynamic> row) async {
    if (await confirm(context, 'ط­ط°ظپ ط§ظ„ط·ط§ظ„ط¨', 'ظ‡ظ„ طھط±ظٹط¯ ط­ط°ظپ ${row['name'] ?? row['student_id']}طں') != true) return;
    try {
      await widget.dio.delete('/students/${row['student_id']}');
      await load();
    } on DioException catch (e) {
      snack(messageFromDio(e), true);
    }
  }

  void snack(String message, bool error) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: error ? Colors.red.shade700 : const Color(0xFF0F766E)),
    );
  }

  @override
  Widget build(BuildContext context) {
    final data = filtered;
    return RefreshIndicator(
      onRefresh: load,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
        children: [
          Header(title: 'ط¥ط¯ط§ط±ط© ط§ظ„ط·ظ„ط§ط¨', count: rows.length, onAdd: () => save(), onRefresh: load),
          const SizedBox(height: 12),
          TextField(
            controller: search,
            onChanged: (_) => setState(() {}),
            decoration: const InputDecoration(prefixIcon: Icon(Icons.search), hintText: 'ط¨ط­ط«', border: OutlineInputBorder()),
          ),
          const SizedBox(height: 12),
          if (loading)
            const Center(child: Padding(padding: EdgeInsets.all(32), child: CircularProgressIndicator()))
          else if (error != null)
            EmptyState(title: 'طھط¹ط°ط± ط§ظ„طھط­ظ…ظٹظ„', message: error!, onRetry: load)
          else if (data.isEmpty)
            const EmptyState(title: 'ظ„ط§ طھظˆط¬ط¯ ط³ط¬ظ„ط§طھ', message: 'ظ„ط§ طھظˆط¬ط¯ ط¨ظٹط§ظ†ط§طھ ط¶ظ…ظ† ظ‡ط°ظ‡ ط§ظ„طµظ„ط§ط­ظٹط©.')
          else
            ...data.map((row) => RecordCard(
                  row: row,
                  keys: const ['student_id', 'name', 'class', 'section', 'stage', 'gender', 'status'],
                  onEdit: () => save(row),
                  onDelete: () => delete(row),
                )),
        ],
      ),
    );
  }
}

class ResourceTab extends StatefulWidget {
  final Dio dio;
  final String title;
  final String endpoint;
  final List<String> displayKeys;
  final List<FieldSpec> fields;
  final bool createAsList;

  const ResourceTab({
    required this.dio,
    required this.title,
    required this.endpoint,
    required this.displayKeys,
    required this.fields,
    this.createAsList = false,
    super.key,
  });

  @override
  State<ResourceTab> createState() => _ResourceTabState();
}

class _ResourceTabState extends State<ResourceTab> {
  List<Map<String, dynamic>> rows = [];
  bool loading = true;
  String? error;

  @override
  void initState() {
    super.initState();
    load();
  }

  Future<void> load() async {
    setState(() {
      loading = true;
      error = null;
    });
    try {
      final res = await widget.dio.get(widget.endpoint);
      if (!mounted) return;
      setState(() => rows = extractList(res.data));
    } on DioException catch (e) {
      if (!mounted) return;
      setState(() => error = messageFromDio(e));
    } finally {
      if (mounted) setState(() => loading = false);
    }
  }

  Future<void> save([Map<String, dynamic>? row]) async {
    final data = await showDialog<Map<String, dynamic>>(
      context: context,
      builder: (_) => ResourceDialog(title: widget.title, fields: widget.fields, initial: row),
    );
    if (data == null) return;
    try {
      if (row == null) {
        await widget.dio.post(widget.endpoint, data: widget.createAsList ? [data] : data);
      } else {
        await widget.dio.put('${widget.endpoint}/${row['id']}', data: data);
      }
      await load();
    } on DioException catch (e) {
      snack(messageFromDio(e), true);
    }
  }

  Future<void> delete(Map<String, dynamic> row) async {
    if (row['id'] == null) {
      snack('ظ‡ط°ط§ ط§ظ„ط³ط¬ظ„ ظ„ط§ ظٹط­طھظˆظٹ id ظ„ظ„ط­ط°ظپ', true);
      return;
    }
    if (await confirm(context, 'ط­ط°ظپ', 'ظ‡ظ„ طھط±ظٹط¯ ط­ط°ظپ ط§ظ„ط³ط¬ظ„طں') != true) return;
    try {
      await widget.dio.delete('${widget.endpoint}/${row['id']}');
      await load();
    } on DioException catch (e) {
      snack(messageFromDio(e), true);
    }
  }

  void snack(String message, bool error) {
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(content: Text(message), backgroundColor: error ? Colors.red.shade700 : const Color(0xFF0F766E)),
    );
  }

  @override
  Widget build(BuildContext context) {
    return RefreshIndicator(
      onRefresh: load,
      child: ListView(
        padding: const EdgeInsets.fromLTRB(16, 16, 16, 96),
        children: [
          Header(title: widget.title, count: rows.length, onAdd: () => save(), onRefresh: load),
          const SizedBox(height: 12),
          if (loading)
            const Center(child: Padding(padding: EdgeInsets.all(32), child: CircularProgressIndicator()))
          else if (error != null)
            EmptyState(title: 'طھط¹ط°ط± ط§ظ„طھط­ظ…ظٹظ„', message: error!, onRetry: load)
          else if (rows.isEmpty)
            const EmptyState(title: 'ظ„ط§ طھظˆط¬ط¯ ط³ط¬ظ„ط§طھ', message: 'ظ„ط§ طھظˆط¬ط¯ ط¨ظٹط§ظ†ط§طھ ط­ط§ظ„ظٹط§ظ‹.')
          else
            ...rows.map((row) => RecordCard(row: row, keys: widget.displayKeys, onEdit: () => save(row), onDelete: () => delete(row))),
        ],
      ),
    );
  }
}

class Header extends StatelessWidget {
  final String title;
  final int count;
  final VoidCallback onAdd;
  final VoidCallback onRefresh;

  const Header({required this.title, required this.count, required this.onAdd, required this.onRefresh, super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: panel(),
      child: Row(
        children: [
          Expanded(child: Text('$title ($count)', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold))),
          IconButton(onPressed: onRefresh, icon: const Icon(Icons.refresh)),
          FilledButton.icon(onPressed: onAdd, icon: const Icon(Icons.add), label: const Text('ط¥ط¶ط§ظپط©')),
        ],
      ),
    );
  }
}

class RecordCard extends StatelessWidget {
  final Map<String, dynamic> row;
  final List<String> keys;
  final VoidCallback onEdit;
  final VoidCallback onDelete;

  const RecordCard({required this.row, required this.keys, required this.onEdit, required this.onDelete, super.key});

  @override
  Widget build(BuildContext context) {
    return Container(
      margin: const EdgeInsets.only(bottom: 8),
      padding: const EdgeInsets.all(12),
      decoration: panel(),
      child: Row(
        children: [
          Expanded(
            child: Wrap(
              spacing: 8,
              runSpacing: 8,
              children: keys.map((k) => Chip(label: Text('$k: ${valueAt(row, k)}'))).toList(),
            ),
          ),
          IconButton(onPressed: onEdit, icon: const Icon(Icons.edit_outlined)),
          IconButton(onPressed: onDelete, color: Colors.red.shade700, icon: const Icon(Icons.delete_outline)),
        ],
      ),
    );
  }
}

class StudentDialog extends StatelessWidget {
  final Map<String, dynamic>? initial;
  final Scope? lockedScope;

  const StudentDialog({this.initial, this.lockedScope, super.key});

  @override
  Widget build(BuildContext context) {
    return ResourceDialog(
      title: initial == null ? 'ط¥ط¶ط§ظپط© ط·ط§ظ„ط¨' : 'طھط¹ط¯ظٹظ„ ط·ط§ظ„ط¨',
      initial: {
        ...?initial,
        if (lockedScope != null) 'stage': lockedScope!.stage,
        if (lockedScope != null) 'gender': lockedScope!.gender,
      },
      fields: [
        FieldSpec('student_id', 'ط±ظ‚ظ… ط§ظ„ط·ط§ظ„ط¨', type: TextInputType.number, enabled: initial == null),
        const FieldSpec('name', 'ط§ط³ظ… ط§ظ„ط·ط§ظ„ط¨'),
        const FieldSpec('email', 'ط§ظ„ط¨ط±ظٹط¯', required: false),
        FieldSpec('password', initial == null ? 'ظƒظ„ظ…ط© ط§ظ„ظ…ط±ظˆط±' : 'ظƒظ„ظ…ط© ظ…ط±ظˆط± ط¬ط¯ظٹط¯ط©', required: initial == null),
        const FieldSpec('class', 'ط§ظ„طµظپ', required: false),
        const FieldSpec('section', 'ط§ظ„ط´ط¹ط¨ط©', required: false),
        const FieldSpec('grade_level', 'ط§ظ„ظ…ط³طھظˆظ‰', required: false),
        FieldSpec('stage', 'ط§ظ„ظ…ط±ط­ظ„ط© primary/secondary/thirdy', enabled: lockedScope == null),
        FieldSpec('gender', 'ط§ظ„ط¬ظ†ط³ male/female', enabled: lockedScope == null),
        const FieldSpec('status', 'ط§ظ„ط­ط§ظ„ط© active/suspended/graduated/withdrawn', required: false),
        const FieldSpec('parent_contact', 'ظ‡ط§طھظپ ظˆظ„ظٹ ط§ظ„ط£ظ…ط±', required: false),
        const FieldSpec('address', 'ط§ظ„ط¹ظ†ظˆط§ظ†', required: false),
        const FieldSpec('nationality', 'ط§ظ„ط¬ظ†ط³ظٹط©', required: false),
      ],
    );
  }
}

class ResourceDialog extends StatefulWidget {
  final String title;
  final List<FieldSpec> fields;
  final Map<String, dynamic>? initial;

  const ResourceDialog({required this.title, required this.fields, this.initial, super.key});

  @override
  State<ResourceDialog> createState() => _ResourceDialogState();
}

class _ResourceDialogState extends State<ResourceDialog> {
  final formKey = GlobalKey<FormState>();
  late final Map<String, TextEditingController> controllers;

  @override
  void initState() {
    super.initState();
    controllers = {
      for (final f in widget.fields) f.key: TextEditingController(text: valueAt(widget.initial ?? {}, f.key)),
    };
  }

  @override
  void dispose() {
    for (final c in controllers.values) {
      c.dispose();
    }
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      title: Text(widget.title),
      content: SizedBox(
        width: 640,
        child: SingleChildScrollView(
          child: Form(
            key: formKey,
            child: Wrap(
              spacing: 12,
              runSpacing: 12,
              children: widget.fields.map((f) {
                return SizedBox(
                  width: 200,
                  child: TextFormField(
                    controller: controllers[f.key],
                    enabled: f.enabled,
                    keyboardType: f.type,
                    decoration: InputDecoration(labelText: f.label, border: const OutlineInputBorder()),
                    validator: (v) => f.required && (v == null || v.trim().isEmpty) ? 'ظ…ط·ظ„ظˆط¨' : null,
                  ),
                );
              }).toList(),
            ),
          ),
        ),
      ),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context), child: const Text('ط¥ظ„ط؛ط§ط،')),
        FilledButton(onPressed: submit, child: const Text('ط­ظپط¸')),
      ],
    );
  }

  void submit() {
    if (!formKey.currentState!.validate()) return;
    final data = <String, dynamic>{};
    for (final f in widget.fields) {
      final value = controllers[f.key]!.text.trim();
      if (value.isNotEmpty) data[f.key] = parseValue(value);
    }
    Navigator.pop(context, data);
  }
}

class FieldSpec {
  final String key;
  final String label;
  final bool required;
  final bool enabled;
  final TextInputType? type;

  const FieldSpec(this.key, this.label, {this.required = true, this.enabled = true, this.type});
}

class EmptyState extends StatelessWidget {
  final String title;
  final String message;
  final VoidCallback? onRetry;

  const EmptyState({required this.title, required this.message, this.onRetry, super.key});

  @override
  Widget build(BuildContext context) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(32),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            const Icon(Icons.inbox_outlined, size: 56, color: Color(0xFF6B7280)),
            const SizedBox(height: 8),
            Text(title, style: Theme.of(context).textTheme.titleMedium),
            const SizedBox(height: 6),
            Text(message, textAlign: TextAlign.center),
            if (onRetry != null) TextButton.icon(onPressed: onRetry, icon: const Icon(Icons.refresh), label: const Text('ط¥ط¹ط§ط¯ط© ط§ظ„ظ…ط­ط§ظˆظ„ط©')),
          ],
        ),
      ),
    );
  }
}

class Scope {
  final String stage;
  final String gender;

  const Scope(this.stage, this.gender);
}

Scope? scopeForRole(String role) {
  final r = role.toLowerCase();
  final stage = r.contains('primary')
      ? 'primary'
      : r.contains('secondary')
          ? 'secondary'
          : (r.contains('thirdy') || r.contains('tertiary'))
              ? 'thirdy'
              : null;
  final gender = r.contains('female')
      ? 'female'
      : r.contains('male')
          ? 'male'
          : null;
  return stage != null && gender != null ? Scope(stage, gender) : null;
}

String titleForRole(String role) {
  final r = role.toLowerCase();
  if (r == 'admin') return 'ظ„ظˆط­ط© ط§ظ„ط¥ط¯ط§ط±ط© ط§ظ„ظƒط§ظ…ظ„ط©';
  final scope = scopeForRole(role);
  if (scope == null) return 'ظ„ظˆط­ط© ط§ظ„ط¥ط¯ط§ط±ط©';
  final stage = {'primary': 'ط§ط¨طھط¯ط§ط¦ظٹ', 'secondary': 'ط¥ط¹ط¯ط§ط¯ظٹ', 'thirdy': 'ط«ط§ظ†ظˆظٹ'}[scope.stage];
  final gender = {'male': 'ط°ظƒظˆط±', 'female': 'ط¥ظ†ط§ط«'}[scope.gender];
  return 'ظ…ظˆط¬ظ‡ $stage $gender';
}

List<Map<String, dynamic>> extractList(dynamic raw) {
  if (raw is List) return raw.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).toList();
  if (raw is Map) {
    for (final key in ['data', 'items', 'payments', 'scores']) {
      final value = raw[key];
      if (value is List) return value.whereType<Map>().map((e) => Map<String, dynamic>.from(e)).toList();
    }
    final data = raw['data'];
    if (data is Map) {
      return data.values.whereType<List>().expand((v) => v).whereType<Map>().map((e) => Map<String, dynamic>.from(e)).toList();
    }
  }
  return [];
}

String valueAt(Map<String, dynamic> row, String key) {
  dynamic value = row;
  for (final part in key.split('.')) {
    if (value is Map) {
      value = value[part];
    } else {
      return '';
    }
  }
  return value?.toString() ?? '';
}

dynamic parseValue(String value) {
  if (value == 'true') return true;
  if (value == 'false') return false;
  return int.tryParse(value) ?? double.tryParse(value) ?? value;
}

String messageFromDio(DioException e) {
  final data = e.response?.data;
  if (data is Map) {
    if (data['message'] != null) return data['message'].toString();
    if (data['error'] != null) return data['error'].toString();
    if (data['errors'] is Map) {
      final first = (data['errors'] as Map).values.whereType<List>().firstOrNull;
      if (first != null && first.isNotEmpty) return first.first.toString();
    }
  }
  return e.message ?? 'طھط¹ط°ط± ط§ظ„ط§طھطµط§ظ„ ط¨ط§ظ„ط®ط§ط¯ظ…';
}

Future<bool?> confirm(BuildContext context, String title, String message) {
  return showDialog<bool>(
    context: context,
    builder: (_) => AlertDialog(
      title: Text(title),
      content: Text(message),
      actions: [
        TextButton(onPressed: () => Navigator.pop(context, false), child: const Text('ط¥ظ„ط؛ط§ط،')),
        FilledButton(onPressed: () => Navigator.pop(context, true), child: const Text('طھط£ظƒظٹط¯')),
      ],
    ),
  );
}

BoxDecoration panel() {
  return BoxDecoration(
    color: Colors.white,
    borderRadius: BorderRadius.circular(8),
    border: Border.all(color: const Color(0xFFE5E7EB)),
  );
}
