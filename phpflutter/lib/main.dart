import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';

void main() {
  runApp(const MyApp());
}

class MyApp extends StatelessWidget {
  const MyApp({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'C.R.U.D.',
      theme: ThemeData(
        colorScheme: ColorScheme.fromSeed(seedColor: Colors.deepPurple),
        useMaterial3: true,
      ),
      home: const CrudApi(title: 'C.R.U.D. padrão API'),
    );
  }
}

class CrudApi extends StatefulWidget {
  const CrudApi({Key? key, required this.title}) : super(key: key);

  final String title;

  @override
  State<CrudApi> createState() => _CrudApiState();
}

class _CrudApiState extends State<CrudApi> {
  final _idController = TextEditingController(text: '1');
  final _nomeController = TextEditingController();
  final _categoriaController = TextEditingController();

  List<Map<String, dynamic>> _data = [];

  @override
  void initState() {
    super.initState();
    get();
  }

  Future<void> get() async {
    try {
      final response = await http.get(Uri.parse('http://127.0.0.1/api/testeApi.php/cliente'));
      if (response.statusCode == 200) {
        final List<dynamic> data = json.decode(response.body);
        setState(() {
          _data = data.cast<Map<String, dynamic>>();
        });
      } else {
        print('Erro na solicitação GET');
      }
    } catch (e) {
      print('Erro ao executar solicitação GET: $e');
    }
  }

  Future<void> post() async {
    try {
      final response = await http.post(Uri.parse('http://localhost/api/testeApi.php/cliente'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({
        'nome': _nomeController.text,
        'categoria': _categoriaController.text,
      }),
      );
      if (response.statusCode == 200) {
        get(); //Atualizar a tabela
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Item adicionado com sucesso')));
      }
    } catch (e) {
      print('Erro ao executar solicitação POST: $e');
    }
  }

  Future<void> delete() async {
    try{
      final response = await http.delete(Uri.parse('http://localhost/api/testeApi.php/cliente/${_idController.text}'),);
      if (response.statusCode == 200) {
        get(); //Atualizar a tabela
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Item excluído com sucesso')));
      }
    } catch (e) {
      print('Erro ao executar a solicitação DELETE: $e');
    }
  }

  Future <void> put() async {
    try {
      final response = await http.put(Uri.parse('http://localhost/api/testeApi.php/cliente/${_idController.text}'),
      headers: {'Content-Type': 'application/json'},
      body: json.encode({
        'nome': _nomeController.text,
        'categoria': _categoriaController.text,
      }),
      );
      if (response.statusCode == 200) {
        get(); //Atualizar a tabelas
        ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text('Item atualizado com sucesso')));
      }
    } catch (e) {
      print('Erro ao executar solicitação PUT $e');
    }
  }

  void selectRow(int id, String nome, String categoria) {
    setState(() {
      _idController.text = id.toString();
      _nomeController.text = nome;
      _categoriaController.text = categoria;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        backgroundColor: Theme.of(context).colorScheme.inversePrimary,
        title: Text(widget.title),
      ),
      body: Padding(
        padding: EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.start,
          children:[
            TextField(
              controller: _idController,
              decoration: InputDecoration(labelText: 'Id'),
              readOnly: true,
            ),
            SizedBox(height: 10),
            TextField(
              controller: _nomeController,
              decoration: InputDecoration(labelText: 'Digite o nome'),
            ),
            SizedBox(height: 10),
            TextField(
              controller: _categoriaController,
              decoration: InputDecoration(labelText: 'Digite a categoria'),
            ),
            SizedBox(height: 20),
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
              children: [
                ElevatedButton(onPressed: get, child: Text('GET')),
                ElevatedButton(onPressed: post, child: Text('POST')),
                ElevatedButton(onPressed: delete, child: Text('DELETE')),
                ElevatedButton(onPressed: put, child: Text('PUT')),
              ]
            ),
            SizedBox(height: 20),
            Expanded(
              child: SingleChildScrollView(
                child: DataTable(
                  columns: [
                    DataColumn(label: Text('ID')),
                    DataColumn(label: Text('Nome')),
                    DataColumn(label: Text('Categoria')),
                  ],
                  rows: _data.map((item) {
                    return DataRow(
                      cells: [
                        DataCell(Text(item['id'].toString()), onTap: () => selectRow(int.parse(item['id']), item['nome'], item['categoria'])),
                        DataCell(Text(item['nome']), onTap: () => selectRow(item['id'], item['nome'], item['categoria'])),
                        DataCell(Text(item['categoria']), onTap: () => selectRow(item['id'], item['nome'], item['categoria'])),
                      ],
                    );
                  }).toList(),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
