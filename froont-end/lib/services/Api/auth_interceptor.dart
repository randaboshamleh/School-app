import 'package:dio/dio.dart';
import 'api_config.dart';

class ApiClient {
  late final Dio dio;

  ApiClient() {
    dio = Dio(BaseOptions(
  baseUrl: kBaseUrl,
  connectTimeout: kRequestTimeout,   
  receiveTimeout: kRequestTimeout,   
  headers: {'Accept': 'application/json'},
));

    dio.interceptors.add(InterceptorsWrapper(
      onRequest: (options, handler) async {
        final token = await getAuthToken();
        if (token != null) options.headers['Authorization'] = 'Bearer $token';
        return handler.next(options);
      },
      onError: (err, handler) async {
        // محاولة تجديد التوكن عند 401
        if (err.response?.statusCode == 401) {
          final refreshed = await _tryRefreshToken();
          if (refreshed) {
            // إعادة تنفيذ الطلب الأصلي بعد تحديث التوكن
            final opts = err.requestOptions;
            final newToken = await getAuthToken();
            if (newToken != null) opts.headers['Authorization'] = 'Bearer $newToken';
            try {
              final cloned = await dio.request(
                opts.path,
                options: Options(
                  method: opts.method,
                  headers: opts.headers,
                ),
                data: opts.data,
                queryParameters: opts.queryParameters,
              );
              return handler.resolve(cloned);
            } catch (e) {
              return handler.next(err);
            }
          }
        }
        return handler.next(err);
      },
    ));
  }

  Future<bool> _tryRefreshToken() async {
    final refreshToken = await getRefreshToken();
    if (refreshToken == null) return false;
    try {
      // استخدم Dio ثاني بدون interceptor لتفادي حلقة لامتناهية
      final refreshDio = Dio(BaseOptions(baseUrl: kBaseUrl));
      final resp = await refreshDio.post('/$kApiVersion/auth/refresh',
          data: {'refresh_token': refreshToken});
      if (resp.statusCode == 200) {
        await saveTokens(
          accessToken: resp.data['access_token'],
          refreshToken: resp.data['refresh_token'],
        );
        return true;
      }
    } catch (_) {}
    return false;
  }
}
