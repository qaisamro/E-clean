import 'package:flutter/material.dart';

/// Wraps a `PageView` child so its state (and the Riverpod providers it
/// subscribes to) survives tab switches. Without it every tab switch
/// unmounts/remounts the page, rebuilding the widget tree and refetching all
/// its data, which makes bottom-navigation feel slow.
class KeepAlivePage extends StatefulWidget {
  const KeepAlivePage({super.key, required this.child});

  final Widget child;

  @override
  State<KeepAlivePage> createState() => _KeepAlivePageState();
}

class _KeepAlivePageState extends State<KeepAlivePage>
    with AutomaticKeepAliveClientMixin {
  @override
  bool get wantKeepAlive => true;

  @override
  Widget build(BuildContext context) {
    super.build(context);
    return widget.child;
  }
}